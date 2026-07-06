<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { openRazorpay } from '@/razorpay.js';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    item: { type: Object, required: true },
    recipient: { type: Object, required: true },
    amount: { type: Number, required: true },
    order: { type: Object, required: true },
    razorpayKey: { type: String, default: null },
    testMode: { type: Boolean, default: true },
    savedAddress: { type: Object, default: null }, // { recipient_name, city } — never the full address
});

const inputClass = 'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
const money = computed(() => `₹${Number(props.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);

const form = useForm({
    message: '',
    use_saved_address: Boolean(props.savedAddress),
    recipient_name: props.recipient.name ?? '',
    address_line: '',
    city: '',
    postal_code: '',
    razorpay_order_id: props.order.id,
    razorpay_payment_id: '',
    razorpay_signature: '',
});

const paying = ref(false);
const payError = ref('');

const pay = async () => {
    if (props.testMode) {
        // STUB: fabricate a successful payment, skip the Razorpay widget.
        form.razorpay_payment_id = 'pay_test_' + Math.random().toString(36).slice(2, 16);
        form.razorpay_signature = 'test_signature';
        form.post(route('checkout.store', props.item.id));
        return;
    }

    // LIVE: open the Razorpay widget, then submit the verified handshake.
    paying.value = true;
    payError.value = '';
    try {
        await openRazorpay({
            key: props.razorpayKey,
            order: props.order,
            description: `Gift: ${props.item.title}`,
            prefill: { name: form.recipient_name || undefined },
            onSuccess: (r) => {
                form.razorpay_order_id = r.razorpay_order_id;
                form.razorpay_payment_id = r.razorpay_payment_id;
                form.razorpay_signature = r.razorpay_signature;
                form.post(route('checkout.store', props.item.id), {
                    onFinish: () => (paying.value = false),
                });
            },
            onDismiss: () => (paying.value = false),
        });
    } catch (e) {
        payError.value = e.message;
        paying.value = false;
    }
};
</script>

<template>
    <Head title="Checkout" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-neutral-900">Send a gift</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div v-if="testMode" class="mb-6 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Test mode — no real payment will be charged. Add Razorpay keys to go live.
                </div>

                <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                    <!-- Delivery form -->
                    <form @submit.prevent="pay" class="order-2 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 lg:order-1">
                        <h3 class="text-base font-bold text-neutral-900">Delivery details</h3>

                        <!-- Saved-address shortcut -->
                        <div v-if="savedAddress" class="mt-4 grid gap-3 sm:grid-cols-2">
                            <button type="button" @click="form.use_saved_address = true"
                                class="rounded-2xl border-2 px-4 py-3.5 text-left transition"
                                :class="form.use_saved_address ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                <span class="block text-sm font-bold text-neutral-900">📍 Their saved address</span>
                                <span class="mt-0.5 block text-xs text-neutral-500">Ships to {{ savedAddress.recipient_name }}<span v-if="savedAddress.city"> in {{ savedAddress.city }}</span> — no address needed</span>
                            </button>
                            <button type="button" @click="form.use_saved_address = false"
                                class="rounded-2xl border-2 px-4 py-3.5 text-left transition"
                                :class="!form.use_saved_address ? 'border-indigo-500 bg-indigo-50/60' : 'border-neutral-200 hover:border-neutral-300'">
                                <span class="block text-sm font-bold text-neutral-900">Enter an address</span>
                                <span class="mt-0.5 block text-xs text-neutral-500">Send it somewhere else</span>
                            </button>
                        </div>
                        <InputError class="mt-1.5" :message="form.errors.use_saved_address" />

                        <p v-if="savedAddress && form.use_saved_address" class="mt-4 rounded-xl bg-neutral-50 px-4 py-3 text-xs leading-5 text-neutral-500">
                            🔒 The full address stays private — we'll ship directly to the one {{ savedAddress.recipient_name }} saved on this wishlist.
                        </p>

                        <div v-if="!savedAddress || !form.use_saved_address" class="mt-5 space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-neutral-800">Recipient name <span class="text-rose-500">*</span></label>
                                <input v-model="form.recipient_name" type="text" :class="inputClass" />
                                <InputError class="mt-1.5" :message="form.errors.recipient_name" />
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
                        </div>

                        <div class="mt-5">
                            <label class="text-sm font-semibold text-neutral-800">Gift message</label>
                            <textarea v-model="form.message" rows="3" :class="inputClass" placeholder="Add a personal note…"></textarea>
                            <InputError class="mt-1.5" :message="form.errors.message" />
                        </div>
                    </form>

                    <!-- Order summary -->
                    <div class="order-1 lg:order-2">
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                            <h3 class="text-base font-bold text-neutral-900">Order summary</h3>
                            <div class="mt-5 flex gap-4">
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-neutral-100">
                                    <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-neutral-900">{{ item.title }}</p>
                                    <p class="mt-0.5 text-xs text-neutral-500">For {{ recipient.name }}</p>
                                </div>
                            </div>
                            <div class="mt-5 space-y-2 border-t border-neutral-100 pt-5 text-sm">
                                <div class="flex justify-between text-neutral-600"><span>Gift amount</span><span>{{ money }}</span></div>
                                <div class="flex justify-between font-bold text-neutral-900"><span>Total</span><span>{{ money }}</span></div>
                            </div>
                            <button
                                type="button"
                                @click="pay"
                                :disabled="form.processing || paying"
                                class="mt-6 w-full rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                            >
                                {{ form.processing || paying ? 'Processing…' : `Pay ${money}` }}
                            </button>
                            <p v-if="payError" class="mt-3 text-center text-xs text-rose-600">{{ payError }}</p>
                            <Link :href="route('wishlists.index')" class="mt-3 block text-center text-xs font-medium text-neutral-400 hover:text-neutral-600">Cancel</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
