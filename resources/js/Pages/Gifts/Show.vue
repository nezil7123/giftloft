<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    gift: { type: Object, required: true },
    isBuyer: { type: Boolean, default: true },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleString(undefined, { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—');

// Simple tracking timeline derived from gift state.
const steps = computed(() => [
    { key: 'completed', label: 'Payment confirmed', done: true, at: props.gift.created_at },
    { key: 'claimed', label: 'Gift reserved', done: !!props.gift.claimed_at, at: props.gift.claimed_at },
    { key: 'delivered', label: 'Delivered', done: !!props.gift.delivered_at, at: props.gift.delivered_at },
]);
const addr = computed(() => props.gift.delivery_address ?? {});
</script>

<template>
    <Head title="Gift details" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('gifts.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← All gifts</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Gift details</h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Summary -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <div class="flex gap-5">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-neutral-100">
                            <img v-if="gift.wishlist_item?.image_url" :src="gift.wishlist_item.image_url" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-3xl">🎁</div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-bold text-neutral-900">{{ gift.wishlist_item?.title || 'Gift' }}</h3>
                            <p class="mt-1 text-sm text-neutral-500">
                                {{ isBuyer ? `Sent to ${gift.recipient?.name}` : `From ${gift.buyer?.name}` }}
                            </p>
                            <p v-if="gift.event" class="mt-0.5 text-xs text-indigo-500">For {{ gift.event.title }}</p>
                            <p class="mt-3 text-xl font-extrabold text-neutral-900">{{ money(gift.amount) }}</p>
                        </div>
                    </div>

                    <div v-if="gift.message" class="mt-6 rounded-2xl bg-neutral-50 p-4 text-sm italic text-neutral-700">"{{ gift.message }}"</div>
                </div>

                <!-- Tracking timeline -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <h3 class="text-base font-bold text-neutral-900">Tracking</h3>
                    <ol class="mt-5 space-y-5">
                        <li v-for="step in steps" :key="step.key" class="flex gap-4">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full" :class="step.done ? 'bg-emerald-500 text-white' : 'bg-neutral-200 text-neutral-400'">
                                <svg v-if="step.done" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span v-else class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" :class="step.done ? 'text-neutral-900' : 'text-neutral-400'">{{ step.label }}</p>
                                <p v-if="step.done" class="text-xs text-neutral-400">{{ fmtDate(step.at) }}</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Delivery + payment -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-neutral-400">Delivery</h3>
                        <p class="mt-3 text-sm font-semibold text-neutral-900">{{ addr.recipient_name || gift.recipient?.name }}</p>
                        <p class="mt-1 text-sm text-neutral-600">{{ addr.address_line }}</p>
                        <p class="text-sm text-neutral-600">{{ [addr.city, addr.postal_code].filter(Boolean).join(' · ') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-neutral-400">Payment</h3>
                        <p class="mt-3 text-sm text-neutral-600">Status: <span class="font-semibold capitalize text-emerald-600">{{ gift.payment?.status || 'paid' }}</span></p>
                        <p class="mt-1 text-sm text-neutral-600">Amount: <span class="font-semibold text-neutral-900">{{ money(gift.payment?.amount ?? gift.amount) }}</span></p>
                        <p v-if="gift.payment?.razorpay_payment_id" class="mt-1 truncate text-xs text-neutral-400">Ref: {{ gift.payment.razorpay_payment_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
