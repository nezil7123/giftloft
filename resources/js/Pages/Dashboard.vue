<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: Object,
    stats: Object,
    recentEvents: { type: Array, default: () => [] },
});

const typeColors = {
    wedding: 'from-rose-500 to-pink-600',
    birthday: 'from-amber-400 to-orange-500',
    engagement: 'from-fuchsia-500 to-purple-600',
    anniversary: 'from-red-400 to-rose-500',
    baby_shower: 'from-sky-400 to-blue-600',
    proposal: 'from-violet-500 to-purple-700',
    graduation: 'from-indigo-500 to-blue-600',
    housewarming: 'from-emerald-400 to-teal-600',
    other: 'from-neutral-400 to-neutral-600',
};
const gradient = (type) => typeColors[type] ?? typeColors.other;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : 'No date set');

const statusBadge = {
    published: 'bg-emerald-100 text-emerald-700',
    draft: 'bg-amber-100 text-amber-700',
};

// The "design" quick action jumps straight into the most recent event if one
// exists; otherwise it sends the user to create their first event.
const designHref = computed(() =>
    props.recentEvents.length ? route('events.design.edit', props.recentEvents[0].id) : route('events.create')
);
const wishlistHref = computed(() => (props.stats.wishlists > 0 ? route('wishlists.index') : route('wishlists.create')));

const quickActions = computed(() => [
    {
        label: 'Create an event',
        description: 'Start planning your next celebration',
        href: route('events.create'),
        gradient: 'from-indigo-500 to-violet-600',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    },
    {
        label: 'Design website or invitation',
        description: 'Pick a template and make it yours',
        href: designHref.value,
        gradient: 'from-fuchsia-500 to-pink-600',
        icon: 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z',
    },
    {
        label: 'Order a gift',
        description: 'Browse curated gifts and shop',
        href: route('public.shop'),
        gradient: 'from-emerald-500 to-teal-600',
        icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
    },
    {
        label: 'Build a wishlist',
        description: 'Curate the gifts you’d love to receive',
        href: wishlistHref.value,
        gradient: 'from-rose-500 to-orange-500',
        icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    },
    {
        label: 'Track orders & gifts',
        description: 'See what you’ve sent and received',
        href: route('orders.index'),
        gradient: 'from-sky-500 to-blue-600',
        icon: 'M20 7L12 3 4 7m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    },
]);

const guides = [
    { slug: 'wishlist', emoji: '🎁', label: 'How wishlists work', gradient: 'from-rose-500 to-orange-500' },
    { slug: 'gifting', emoji: '🛍️', label: 'How to send a gift', gradient: 'from-emerald-500 to-teal-600' },
    { slug: 'website', emoji: '🌐', label: 'How to create a website', gradient: 'from-fuchsia-500 to-pink-600' },
    { slug: 'invitation', emoji: '💌', label: 'How to create an invitation', gradient: 'from-violet-500 to-purple-700' },
];

const stats = computed(() => [
    { label: 'Events', value: props.stats.events },
    { label: 'Wishlists', value: props.stats.wishlists },
    { label: 'Gifts', value: props.stats.gifts },
    { label: 'Orders', value: props.stats.orders },
]);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Dashboard</h2>
                <p class="text-sm text-neutral-500">Everything you need, in one place.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Greeting -->
                <div class="rounded-[2rem] bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 p-8 text-white shadow-sm sm:p-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/70">Welcome back</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ user.name.split(' ')[0] }} 👋</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-white/80">Plan an event, put together a wishlist, or send a gift — pick up right where you left off.</p>
                </div>

                <!-- Quick actions -->
                <h3 class="mb-4 mt-10 text-sm font-bold uppercase tracking-wide text-neutral-500">Quick actions</h3>
                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
                    <Link v-for="action in quickActions" :key="action.label" :href="action.href"
                        class="group flex flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br text-white" :class="action.gradient">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="action.icon" /></svg>
                        </div>
                        <p class="mt-3.5 text-sm font-bold text-neutral-900">{{ action.label }}</p>
                        <p class="mt-1 text-xs leading-5 text-neutral-500">{{ action.description }}</p>
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 opacity-0 transition group-hover:opacity-100">
                            Go
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </span>
                    </Link>
                </div>

                <!-- How it works -->
                <div class="mb-4 mt-10 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-neutral-500">New here? See how it works</h3>
                    <Link :href="route('help.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">All guides →</Link>
                </div>
                <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                    <Link v-for="guide in guides" :key="guide.slug" :href="route('help.show', guide.slug)"
                        class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-16 items-center justify-center bg-gradient-to-br text-3xl" :class="guide.gradient">{{ guide.emoji }}</div>
                        <div class="p-4">
                            <p class="text-sm font-bold text-neutral-900">{{ guide.label }}</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 opacity-0 transition group-hover:opacity-100">
                                See how
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Stats -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <div v-for="s in stats" :key="s.label" class="flex min-w-[7.5rem] flex-1 items-center justify-between rounded-2xl bg-white px-5 py-4 shadow-sm ring-1 ring-neutral-200/70">
                        <span class="text-sm font-medium text-neutral-500">{{ s.label }}</span>
                        <span class="text-xl font-black tabular-nums text-neutral-900">{{ s.value }}</span>
                    </div>
                </div>

                <!-- Your events -->
                <div class="mt-10 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-neutral-500">Your events</h3>
                    <Link v-if="recentEvents.length" :href="route('events.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">View all →</Link>
                </div>

                <div v-if="recentEvents.length" class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="event in recentEvents" :key="event.id" :href="route('events.show', event.id)"
                        class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative h-20 bg-gradient-to-br" :class="gradient(event.type)">
                            <span class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="statusBadge[event.status] ?? 'bg-white/20 text-white'">{{ event.status }}</span>
                        </div>
                        <div class="p-5">
                            <p class="truncate text-sm font-bold text-neutral-900">{{ event.title }}</p>
                            <p class="mt-1 text-xs text-neutral-500">{{ fmtDate(event.starts_at) }}</p>
                            <div class="mt-4 flex items-center gap-3 border-t border-neutral-100 pt-3">
                                <Link :href="route('events.design.edit', event.id)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500" @click.stop>Design</Link>
                                <span class="text-neutral-200">·</span>
                                <span class="text-xs font-semibold text-neutral-500 group-hover:text-neutral-800">Manage →</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Onboarding empty state -->
                <div v-else class="mt-4 rounded-[1.75rem] border border-dashed border-neutral-300 bg-white px-8 py-12 text-center">
                    <p class="text-base font-bold text-neutral-900">You haven't created an event yet</p>
                    <p class="mt-2 text-sm text-neutral-500">Create one to get a shareable invitation page, a wishlist, and gift tracking — all in one place.</p>
                    <Link :href="route('events.create')" class="mt-5 inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Create your first event</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
