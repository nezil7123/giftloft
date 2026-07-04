<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    orders: { type: Array, default: () => [] },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '—');

const statusBadge = {
    completed: 'bg-emerald-100 text-emerald-700',
    pending: 'bg-amber-100 text-amber-700',
    refunded: 'bg-neutral-100 text-neutral-500',
};
</script>

<template>
    <Head title="My Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">My orders</h2>
                <p class="text-sm text-neutral-500">Gifts you've bought from the shop, for yourself or sent personally.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div v-if="orders.length" class="space-y-4">
                    <Link v-for="order in orders" :key="order.id" :href="route('orders.show', order.id)"
                        class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-xl">
                            {{ order.is_gift ? '🎁' : '🛍️' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-neutral-900">
                                {{ order.items.map((i) => i.name).join(', ') }}
                            </p>
                            <p class="mt-1 text-xs text-neutral-500">
                                {{ order.is_gift ? `Sent to ${order.recipient_name}` : 'Shipped to you' }} · {{ fmtDate(order.created_at) }}
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="statusBadge[order.status] ?? 'bg-neutral-100 text-neutral-500'">{{ order.status }}</span>
                        <span class="w-24 shrink-0 text-right text-sm font-bold text-neutral-900">{{ money(order.total ?? order.subtotal) }}</span>
                    </Link>
                </div>

                <div v-else class="rounded-[1.75rem] border border-dashed border-neutral-300 bg-white px-8 py-16 text-center">
                    <p class="text-lg font-bold text-neutral-900">No orders yet</p>
                    <p class="mt-2 text-sm text-neutral-500">Buy something lovely from the shop.</p>
                    <Link :href="route('public.shop')" class="mt-5 inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">Browse the shop</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
