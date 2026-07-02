<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, required: true },
    recentUsers: { type: Array, default: () => [] },
    recentGifts: { type: Array, default: () => [] },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '—');
</script>

<template>
    <Head title="Admin — Overview" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Super Admin</h2>
                <p class="text-sm text-neutral-500">Everything happening on Gift Loft, at a glance.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <!-- Stat tiles -->
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="gl-enter rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-6 text-white shadow-lg shadow-indigo-600/20">
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/60">Revenue (test)</p>
                        <p class="mt-3 text-3xl font-black tabular-nums">{{ money(stats.revenue) }}</p>
                        <p class="mt-1 text-xs text-white/50">{{ stats.gifts }} gifts sent</p>
                    </div>
                    <div class="gl-enter gl-d1 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Users</p>
                        <p class="mt-3 text-3xl font-black tabular-nums text-neutral-900">{{ stats.users }}</p>
                        <Link :href="route('admin.users.index')" class="mt-1 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-500">Manage →</Link>
                    </div>
                    <div class="gl-enter gl-d2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Events</p>
                        <p class="mt-3 text-3xl font-black tabular-nums text-neutral-900">{{ stats.events }}</p>
                        <p class="mt-1 text-xs text-neutral-400">{{ stats.published_events }} live · {{ stats.wishlists }} registries</p>
                    </div>
                    <div class="gl-enter gl-d3 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Shop products</p>
                        <p class="mt-3 text-3xl font-black tabular-nums text-neutral-900">{{ stats.products }}</p>
                        <Link :href="route('admin.products.index')" class="mt-1 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-500">{{ stats.active_products }} active · Manage →</Link>
                    </div>
                </div>

                <!-- Recent activity -->
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-7">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-neutral-900">Latest sign-ups</h3>
                            <Link :href="route('admin.users.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">All users →</Link>
                        </div>
                        <div class="mt-4 divide-y divide-neutral-100">
                            <div v-for="u in recentUsers" :key="u.id" class="flex items-center gap-3 py-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-600">{{ u.name?.charAt(0) ?? '?' }}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-neutral-900">{{ u.name }} <span v-if="u.is_admin" class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">ADMIN</span></p>
                                    <p class="truncate text-xs text-neutral-400">{{ u.email }}</p>
                                </div>
                                <p class="shrink-0 text-xs text-neutral-400">{{ fmtDate(u.created_at) }}</p>
                            </div>
                            <p v-if="!recentUsers.length" class="py-6 text-center text-sm text-neutral-400">No users yet.</p>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-7">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-neutral-900">Latest gifts</h3>
                            <Link :href="route('admin.orders.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">All orders →</Link>
                        </div>
                        <div class="mt-4 divide-y divide-neutral-100">
                            <div v-for="g in recentGifts" :key="g.id" class="flex items-center gap-3 py-3">
                                <span class="text-xl">🎁</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-neutral-900">{{ g.wishlist_item?.title ?? 'Gift' }}</p>
                                    <p class="truncate text-xs text-neutral-400">{{ g.buyer?.name }} → {{ g.recipient?.name }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-bold tabular-nums text-neutral-900">{{ money(g.amount) }}</p>
                            </div>
                            <p v-if="!recentGifts.length" class="py-6 text-center text-sm text-neutral-400">No gifts yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
