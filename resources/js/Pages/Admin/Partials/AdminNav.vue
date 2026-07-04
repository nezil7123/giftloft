<script setup>
import { Link } from '@inertiajs/vue3';

const tabs = [
    { label: 'Overview',   icon: '📊', route: 'admin.dashboard',        current: 'admin.dashboard' },
    { label: 'Products',   icon: '🎁', route: 'admin.products.index',   current: 'admin.products.*' },
    { label: 'Gift Addons', icon: '🎀', route: 'admin.gift-addons.index', current: 'admin.gift-addons.*' },
    { label: 'Users',      icon: '👥', route: 'admin.users.index',      current: 'admin.users.*' },
    { label: 'Moderation', icon: '🛡️', route: 'admin.moderation.index', current: 'admin.moderation.*' },
    { label: 'Orders',     icon: '💳', route: 'admin.orders.index',     current: 'admin.orders.*' },
];
</script>

<template>
    <div class="mb-8 flex gap-1.5 overflow-x-auto rounded-2xl bg-white p-1.5 shadow-sm ring-1 ring-neutral-200/70">
        <Link
            v-for="tab in tabs"
            :key="tab.route"
            :href="route(tab.route)"
            class="flex shrink-0 items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
            :class="route().current(tab.current) ? 'bg-neutral-950 text-white shadow-sm' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900'"
        >
            <span>{{ tab.icon }}</span>{{ tab.label }}
        </Link>
    </div>
</template>
