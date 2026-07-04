<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    order: { type: Object, required: true },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleString(undefined, { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—');
</script>

<template>
    <Head title="Order details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-neutral-900">Order #{{ order.id }}</h2>
                <Link :href="route('orders.index')" class="text-sm font-medium text-neutral-500 hover:text-neutral-900">← My orders</Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-2xl">{{ order.is_gift ? '🎁' : '🎉' }}</div>
                        <div>
                            <p class="text-base font-bold text-neutral-900">{{ order.is_gift ? 'Gift sent!' : 'Order confirmed!' }}</p>
                            <p class="text-xs text-neutral-500">Placed {{ fmtDate(order.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="mt-6 divide-y divide-neutral-100 border-t border-neutral-100">
                        <div v-for="item in order.items" :key="item.id" class="flex gap-4 py-4">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-neutral-100">
                                <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="h-full w-full object-cover" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-neutral-900">{{ item.name }}</p>
                                <p class="mt-0.5 text-xs text-neutral-500">Qty {{ item.quantity }} · {{ money(item.price) }} each</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-neutral-700">{{ money(item.price * item.quantity) }}</span>
                        </div>
                    </div>

                    <!-- Gift addons -->
                    <div v-if="order.addons?.length" class="divide-y divide-neutral-100 border-t border-neutral-100">
                        <div v-for="addon in order.addons" :key="addon.id" class="flex items-start justify-between gap-4 py-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <div v-if="addon.image" class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-neutral-100">
                                    <img :src="addon.image" :alt="addon.name" class="h-full w-full object-cover" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-neutral-800">{{ addon.name }}</p>
                                    <p v-if="addon.note" class="mt-0.5 text-xs italic leading-5 text-neutral-500">“{{ addon.note }}”</p>
                                </div>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-neutral-700">{{ money(addon.price) }}</span>
                        </div>
                    </div>

                    <div class="mt-2 space-y-1 border-t border-neutral-100 pt-4">
                        <div v-if="order.addons?.length" class="flex justify-between text-sm text-neutral-500">
                            <span>Subtotal</span>
                            <span>{{ money(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-neutral-900">
                            <span>Total</span>
                            <span>{{ money(order.total ?? order.subtotal) }}</span>
                        </div>
                    </div>

                    <!-- Delivery -->
                    <div class="mt-8 grid gap-6 border-t border-neutral-100 pt-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">{{ order.is_gift ? 'Sent to' : 'Delivery address' }}</p>
                            <p class="mt-2 text-sm font-semibold text-neutral-900">{{ order.recipient_name }}</p>
                            <p class="text-sm text-neutral-600">{{ order.address_line }}</p>
                            <p class="text-sm text-neutral-600">{{ order.city }}, {{ order.postal_code }}</p>
                        </div>
                        <div v-if="order.is_gift && order.gift_message">
                            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Gift message</p>
                            <p class="mt-2 text-sm italic leading-6 text-neutral-600">“{{ order.gift_message }}”</p>
                        </div>
                        <div v-if="order.payment">
                            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Payment ref</p>
                            <p class="mt-2 font-mono text-sm text-neutral-600">{{ order.payment.razorpay_payment_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
