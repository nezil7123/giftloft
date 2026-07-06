<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { openRazorpay } from '@/razorpay.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    items: { type: Array, required: true },
    subtotal: { type: Number, required: true },
    order: { type: Object, required: true },
    razorpayKey: { type: String, default: null },
    testMode: { type: Boolean, default: true },
    self: { type: Object, required: true },
    giftAddons: { type: Object, default: () => ({}) },
});

const inputClass = 'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;

const packagingOptions = computed(() => props.giftAddons.packaging ?? []);
const stickerOptions = computed(() => props.giftAddons.message_sticker ?? []);
const cardOptions = computed(() => props.giftAddons.custom_card ?? []);
const defaultPackagingId = computed(() => packagingOptions.value.find((a) => a.is_default)?.id ?? packagingOptions.value[0]?.id ?? null);

// Sticker/card are template pickers with an explicit "none" tile up front.
const stickerChoices = computed(() => [{ id: null, name: 'No sticker', price: 0, image: null }, ...stickerOptions.value]);
const cardChoices = computed(() => [{ id: null, name: 'No card', price: 0, image: null }, ...cardOptions.value]);

const form = useForm({
    is_gift: false,
    recipient_name: props.self.name ?? '',
    recipient_phone: props.self.phone ?? '',
    recipient_email: props.self.email ?? '',
    address_line: props.self.address_line ?? '',
    city: props.self.city ?? '',
    postal_code: props.self.postal_code ?? '',
    gift_message: '',
    packaging_addon_id: null,
    message_sticker_addon_id: null,
    message_sticker_note: '',
    custom_card_addon_id: null,
    custom_card_note: '',
    razorpay_order_id: props.order.id,
    razorpay_payment_id: '',
    razorpay_signature: '',
});

// Switching modes swaps the address between "me" and a blank slate for the recipient,
// and only offers packaging/extras while sending as a gift.
watch(() => form.is_gift, (isGift) => {
    if (isGift) {
        form.recipient_name = '';
        form.recipient_phone = '';
        form.recipient_email = '';
        form.address_line = '';
        form.city = '';
        form.postal_code = '';
        form.packaging_addon_id = defaultPackagingId.value;
    } else {
        form.recipient_name = props.self.name ?? '';
        form.recipient_phone = props.self.phone ?? '';
        form.recipient_email = props.self.email ?? '';
        form.address_line = props.self.address_line ?? '';
        form.city = props.self.city ?? '';
        form.postal_code = props.self.postal_code ?? '';
        form.packaging_addon_id = null;
        form.message_sticker_addon_id = null;
        form.message_sticker_note = '';
        form.custom_card_addon_id = null;
        form.custom_card_note = '';
    }
});

const selectedPackaging = computed(() => packagingOptions.value.find((a) => a.id === form.packaging_addon_id));
const selectedSticker = computed(() => stickerOptions.value.find((a) => a.id === form.message_sticker_addon_id) ?? null);
const selectedCard = computed(() => cardOptions.value.find((a) => a.id === form.custom_card_addon_id) ?? null);
const addonsTotal = computed(() => {
    if (!form.is_gift) return 0;
    return (Number(selectedPackaging.value?.price) || 0) + (Number(selectedSticker.value?.price) || 0) + (Number(selectedCard.value?.price) || 0);
});
const grandTotal = computed(() => props.subtotal + addonsTotal.value);
const total = computed(() => money(grandTotal.value));

const selectSticker = (choice) => {
    form.message_sticker_addon_id = choice.id;
    if (!choice.id) form.message_sticker_note = '';
};
const selectCard = (choice) => {
    form.custom_card_addon_id = choice.id;
    if (!choice.id) form.custom_card_note = '';
};

const paying = ref(false);
const payError = ref('');

const pay = async () => {
    if (props.testMode) {
        // STUB: fabricate a successful payment, skip the Razorpay widget.
        form.razorpay_payment_id = 'pay_test_' + Math.random().toString(36).slice(2, 16);
        form.razorpay_signature = 'test_signature';
        form.post(route('checkout.cart.store'));
        return;
    }

    // LIVE: create the order at pay time (so it's priced with the selected
    // addons), open the widget, then submit the verified handshake.
    paying.value = true;
    payError.value = '';
    try {
        const { data } = await window.axios.post(route('checkout.cart.order'), {
            is_gift: form.is_gift,
            packaging_addon_id: form.packaging_addon_id,
            message_sticker_addon_id: form.message_sticker_addon_id,
            custom_card_addon_id: form.custom_card_addon_id,
        });

        await openRazorpay({
            key: data.key,
            order: data.order,
            description: 'Gift Loft order',
            prefill: { name: form.recipient_name || undefined, email: form.recipient_email || undefined, contact: form.recipient_phone || undefined },
            onSuccess: (r) => {
                form.razorpay_order_id = r.razorpay_order_id;
                form.razorpay_payment_id = r.razorpay_payment_id;
                form.razorpay_signature = r.razorpay_signature;
                form.post(route('checkout.cart.store'), {
                    onFinish: () => (paying.value = false),
                });
            },
            onDismiss: () => (paying.value = false),
        });
    } catch (e) {
        payError.value = e.response?.data?.message ?? e.message ?? 'Payment could not be started.';
        paying.value = false;
    }
};
</script>

<template>
    <Head title="Checkout" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-neutral-900">Checkout</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div v-if="testMode" class="mb-6 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Test mode — no real payment will be charged. Add Razorpay keys to go live.
                </div>

                <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                    <form @submit.prevent="pay" class="order-2 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 lg:order-1">
                        <!-- Buy for me / gift toggle -->
                        <h3 class="text-base font-bold text-neutral-900">Who is this for?</h3>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <button type="button" @click="form.is_gift = false"
                                class="rounded-2xl border-2 px-4 py-3.5 text-left transition"
                                :class="!form.is_gift ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                <span class="block text-sm font-bold text-neutral-900">Ship to me</span>
                                <span class="block text-xs text-neutral-500">Deliver to my own address</span>
                            </button>
                            <button type="button" @click="form.is_gift = true"
                                class="rounded-2xl border-2 px-4 py-3.5 text-left transition"
                                :class="form.is_gift ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                <span class="block text-sm font-bold text-neutral-900">Send as a gift 🎁</span>
                                <span class="block text-xs text-neutral-500">Ship directly to someone else</span>
                            </button>
                        </div>

                        <h3 class="mt-7 text-base font-bold text-neutral-900">{{ form.is_gift ? "Recipient's details" : 'Delivery details' }}</h3>
                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-neutral-800">{{ form.is_gift ? "Recipient's name" : 'Your name' }} <span class="text-rose-500">*</span></label>
                                <input v-model="form.recipient_name" type="text" :class="inputClass" />
                                <InputError class="mt-1.5" :message="form.errors.recipient_name" />
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-neutral-800">Phone</label>
                                    <input v-model="form.recipient_phone" type="text" :class="inputClass" />
                                    <InputError class="mt-1.5" :message="form.errors.recipient_phone" />
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-neutral-800">Email</label>
                                    <input v-model="form.recipient_email" type="email" :class="inputClass" />
                                    <InputError class="mt-1.5" :message="form.errors.recipient_email" />
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-neutral-800">Address <span class="text-rose-500">*</span></label>
                                <input v-model="form.address_line" type="text" :class="inputClass" placeholder="Street, apartment, etc." />
                                <InputError class="mt-1.5" :message="form.errors.address_line" />
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-neutral-800">City <span class="text-rose-500">*</span></label>
                                    <input v-model="form.city" type="text" :class="inputClass" />
                                    <InputError class="mt-1.5" :message="form.errors.city" />
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-neutral-800">Postal code <span class="text-rose-500">*</span></label>
                                    <input v-model="form.postal_code" type="text" :class="inputClass" />
                                    <InputError class="mt-1.5" :message="form.errors.postal_code" />
                                </div>
                            </div>
                            <div v-if="form.is_gift">
                                <label class="text-sm font-semibold text-neutral-800">Gift message</label>
                                <textarea v-model="form.gift_message" rows="3" :class="inputClass" placeholder="Add a personal note…"></textarea>
                                <InputError class="mt-1.5" :message="form.errors.gift_message" />
                            </div>
                        </div>

                        <!-- Gift packaging & extras -->
                        <div v-if="form.is_gift" class="mt-7 border-t border-neutral-100 pt-7">
                            <h3 class="text-base font-bold text-neutral-900">Gift packaging &amp; extras</h3>
                            <p class="text-xs text-neutral-500">Optional touches to make it feel special — each adds to the total.</p>

                            <!-- Packaging -->
                            <div v-if="packagingOptions.length" class="mt-4">
                                <label class="text-sm font-semibold text-neutral-800">Packaging — pick a template</label>
                                <div class="mt-2 grid gap-2.5 sm:grid-cols-2">
                                    <button v-for="a in packagingOptions" :key="a.id" type="button" @click="form.packaging_addon_id = a.id"
                                        class="flex items-center gap-3 rounded-xl border-2 p-2.5 text-left transition"
                                        :class="form.packaging_addon_id === a.id ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-neutral-100">
                                            <img v-if="a.image" :src="a.image" :alt="a.name" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-neutral-900">{{ a.name }}</span>
                                            <span class="block text-xs text-neutral-500">{{ Number(a.price) > 0 ? `+${money(a.price)}` : 'Free' }}</span>
                                        </div>
                                    </button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.packaging_addon_id" />

                                <div v-if="selectedPackaging?.image" class="mt-3 flex items-center gap-3 rounded-xl bg-neutral-50 p-3">
                                    <img :src="selectedPackaging.image" :alt="selectedPackaging.name" class="h-16 w-16 shrink-0 rounded-lg object-cover" />
                                    <p class="text-xs text-neutral-500">Preview of <span class="font-semibold text-neutral-700">{{ selectedPackaging.name }}</span> — {{ selectedPackaging.description }}</p>
                                </div>
                            </div>

                            <!-- Message sticker -->
                            <div v-if="stickerOptions.length" class="mt-6">
                                <label class="text-sm font-semibold text-neutral-800">Message sticker on box — pick a design</label>
                                <div class="mt-2 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                                    <button v-for="c in stickerChoices" :key="c.id ?? 'none'" type="button" @click="selectSticker(c)"
                                        class="rounded-xl border-2 p-2 text-center transition"
                                        :class="form.message_sticker_addon_id === c.id ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                        <div class="mx-auto h-16 w-16 overflow-hidden rounded-lg bg-neutral-100">
                                            <img v-if="c.image" :src="c.image" :alt="c.name" class="h-full w-full object-cover" />
                                            <span v-else class="flex h-full w-full items-center justify-center text-lg text-neutral-300">—</span>
                                        </div>
                                        <span class="mt-1.5 block truncate text-[11px] font-semibold text-neutral-800">{{ c.name }}</span>
                                        <span v-if="Number(c.price) > 0" class="block text-[10px] text-neutral-500">+{{ money(c.price) }}</span>
                                    </button>
                                </div>

                                <div v-if="selectedSticker" class="mt-3">
                                    <textarea v-model="form.message_sticker_note" rows="2" maxlength="200" :class="inputClass" placeholder="What should the sticker say?"></textarea>
                                    <InputError class="mt-1.5" :message="form.errors.message_sticker_note" />

                                    <!-- Live preview: template image with the buyer's text overlaid -->
                                    <div class="relative mx-auto mt-3 h-36 w-36 overflow-hidden rounded-full ring-1 ring-neutral-200">
                                        <img :src="selectedSticker.image" :alt="selectedSticker.name" class="h-full w-full object-cover" />
                                        <div class="absolute inset-0 flex items-center justify-center p-4">
                                            <p class="line-clamp-3 text-center text-[11px] font-semibold leading-snug text-neutral-800 [text-shadow:0_1px_2px_white,0_-1px_2px_white]">{{ form.message_sticker_note || 'Your message here' }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-1.5 text-center text-[11px] text-neutral-400">Preview — actual print may vary slightly.</p>
                                </div>
                            </div>

                            <!-- Custom card -->
                            <div v-if="cardOptions.length" class="mt-6">
                                <label class="text-sm font-semibold text-neutral-800">Custom card inside — pick a design</label>
                                <div class="mt-2 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                                    <button v-for="c in cardChoices" :key="c.id ?? 'none'" type="button" @click="selectCard(c)"
                                        class="rounded-xl border-2 p-2 text-center transition"
                                        :class="form.custom_card_addon_id === c.id ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                        <div class="mx-auto h-16 w-16 overflow-hidden rounded-lg bg-neutral-100">
                                            <img v-if="c.image" :src="c.image" :alt="c.name" class="h-full w-full object-cover" />
                                            <span v-else class="flex h-full w-full items-center justify-center text-lg text-neutral-300">—</span>
                                        </div>
                                        <span class="mt-1.5 block truncate text-[11px] font-semibold text-neutral-800">{{ c.name }}</span>
                                        <span v-if="Number(c.price) > 0" class="block text-[10px] text-neutral-500">+{{ money(c.price) }}</span>
                                    </button>
                                </div>

                                <div v-if="selectedCard" class="mt-3">
                                    <textarea v-model="form.custom_card_note" rows="3" maxlength="500" :class="inputClass" placeholder="What should the card say?"></textarea>
                                    <InputError class="mt-1.5" :message="form.errors.custom_card_note" />

                                    <!-- Live preview: template image with the buyer's text overlaid -->
                                    <div class="relative mx-auto mt-3 h-44 w-32 overflow-hidden rounded-lg ring-1 ring-neutral-200">
                                        <img :src="selectedCard.image" :alt="selectedCard.name" class="h-full w-full object-cover" />
                                        <div class="absolute inset-x-0 bottom-3 flex justify-center px-3">
                                            <p class="line-clamp-4 text-center text-[10px] font-medium leading-snug text-neutral-800 [text-shadow:0_1px_2px_white,0_-1px_2px_white]">{{ form.custom_card_note || 'Your message here' }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-1.5 text-center text-[11px] text-neutral-400">Preview — actual print may vary slightly.</p>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Order summary -->
                    <div class="order-1 lg:order-2">
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                            <h3 class="text-base font-bold text-neutral-900">Order summary</h3>
                            <div class="mt-5 space-y-4">
                                <div v-for="row in items" :key="row.product.id" class="flex gap-3">
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-neutral-100">
                                        <img v-if="row.product.image_url" :src="row.product.image_url" :alt="row.product.name" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold text-neutral-900">{{ row.product.name }}</p>
                                        <p class="mt-0.5 text-xs text-neutral-500">Qty {{ row.quantity }}</p>
                                    </div>
                                    <span class="shrink-0 text-sm font-semibold text-neutral-700">{{ money(row.line_total) }}</span>
                                </div>
                            </div>
                            <div class="mt-5 space-y-2 border-t border-neutral-100 pt-5 text-sm">
                                <div class="flex justify-between text-neutral-600"><span>Subtotal</span><span>{{ money(subtotal) }}</span></div>
                                <div v-if="selectedPackaging && Number(selectedPackaging.price) > 0" class="flex justify-between text-neutral-600"><span>{{ selectedPackaging.name }}</span><span>{{ money(selectedPackaging.price) }}</span></div>
                                <div v-if="selectedSticker" class="flex justify-between text-neutral-600"><span>{{ selectedSticker.name }}</span><span>{{ money(selectedSticker.price) }}</span></div>
                                <div v-if="selectedCard" class="flex justify-between text-neutral-600"><span>{{ selectedCard.name }}</span><span>{{ money(selectedCard.price) }}</span></div>
                                <div class="flex justify-between border-t border-neutral-100 pt-2 font-bold text-neutral-900"><span>Total</span><span>{{ total }}</span></div>
                            </div>
                            <button
                                type="button"
                                @click="pay"
                                :disabled="form.processing || paying"
                                class="mt-6 w-full rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                            >
                                {{ form.processing || paying ? 'Processing…' : `Pay ${total}` }}
                            </button>
                            <p v-if="payError" class="mt-3 text-center text-xs text-rose-600">{{ payError }}</p>
                            <Link :href="route('cart.index')" class="mt-3 block text-center text-xs font-medium text-neutral-400 hover:text-neutral-600">Back to cart</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
