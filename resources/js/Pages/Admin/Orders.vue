<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import Pager from './Partials/Pager.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    orders: { type: Object, required: true }, // paginator (wishlist-item gifts)
    shopOrders: { type: Object, required: true }, // paginator (shop cart orders)
    totals: { type: Object, required: true },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleString(undefined, { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—');

const statusBadge = {
    completed: 'bg-emerald-100 text-emerald-700',
    pending: 'bg-amber-100 text-amber-700',
    refunded: 'bg-neutral-100 text-neutral-500',
};
</script>

<template>
    <Head title="Admin — Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Orders & payments</h2>
                <p class="text-sm text-neutral-500">Every gift purchased on the platform.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <!-- Totals -->
                <div class="mb-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 p-6 text-white shadow-lg shadow-emerald-600/20">
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/60">Total revenue (test)</p>
                        <p class="mt-3 text-3xl font-black tabular-nums">{{ money(totals.revenue) }}</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Orders</p>
                        <p class="mt-3 text-3xl font-black tabular-nums text-neutral-900">{{ totals.orders }}</p>
                        <p class="mt-1 text-xs text-neutral-400">{{ totals.completed }} completed</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Successful payments</p>
                        <p class="mt-3 text-3xl font-black tabular-nums text-neutral-900">{{ totals.payments }}</p>
                    </div>
                </div>

                <!-- Wishlist-item gifts -->
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-neutral-500">Wishlist gifts</h3>
                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">Gift</th>
                                <th class="px-4 py-4 font-semibold">From → To</th>
                                <th class="px-4 py-4 font-semibold">Event</th>
                                <th class="px-4 py-4 font-semibold">Payment ref</th>
                                <th class="px-4 py-4 font-semibold">Date</th>
                                <th class="px-4 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 text-right font-semibold">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="o in orders.data" :key="o.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5 font-semibold text-neutral-900">{{ o.wishlist_item?.title ?? 'Gift' }}</td>
                                <td class="px-4 py-3.5">
                                    <p class="text-neutral-700">{{ o.buyer?.name }}</p>
                                    <p class="text-xs text-neutral-400">→ {{ o.recipient?.name }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-500">{{ o.event?.title ?? '—' }}</td>
                                <td class="px-4 py-3.5 font-mono text-xs text-neutral-400">{{ o.payment?.razorpay_payment_id ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-neutral-500">{{ fmtDate(o.created_at) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="statusBadge[o.status] ?? 'bg-neutral-100 text-neutral-500'">{{ o.status }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-bold tabular-nums text-neutral-900">{{ money(o.amount) }}</td>
                            </tr>
                            <tr v-if="!orders.data.length">
                                <td colspan="7" class="px-6 py-12 text-center text-neutral-400">No wishlist gifts yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pager :paginator="orders" />

                <!-- Shop cart orders -->
                <h3 class="mb-3 mt-10 text-sm font-bold uppercase tracking-wide text-neutral-500">Shop orders</h3>
                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">Items</th>
                                <th class="px-4 py-4 font-semibold">Buyer</th>
                                <th class="px-4 py-4 font-semibold">Delivery</th>
                                <th class="px-4 py-4 font-semibold">Payment ref</th>
                                <th class="px-4 py-4 font-semibold">Date</th>
                                <th class="px-4 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 text-right font-semibold">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="o in shopOrders.data" :key="o.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5 font-semibold text-neutral-900">
                                    {{ o.items.map((i) => i.name).join(', ') }}
                                    <p v-if="o.addons?.length" class="mt-0.5 text-xs font-normal text-neutral-400">+ {{ o.addons.map((a) => a.name).join(', ') }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-700">{{ o.user?.name }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide" :class="o.is_gift ? 'bg-indigo-100 text-indigo-700' : 'bg-neutral-100 text-neutral-500'">{{ o.is_gift ? 'Gift 🎁' : 'Self' }}</span>
                                    <p class="mt-1 text-xs text-neutral-400">{{ o.recipient_name }}</p>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-xs text-neutral-400">{{ o.payment?.razorpay_payment_id ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-neutral-500">{{ fmtDate(o.created_at) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="statusBadge[o.status] ?? 'bg-neutral-100 text-neutral-500'">{{ o.status }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-bold tabular-nums text-neutral-900">{{ money(o.total ?? o.subtotal) }}</td>
                            </tr>
                            <tr v-if="!shopOrders.data.length">
                                <td colspan="7" class="px-6 py-12 text-center text-neutral-400">No shop orders yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pager :paginator="shopOrders" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
