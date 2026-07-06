<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    profile: { type: Object, required: true },
    events: { type: Array, default: () => [] },
    wishlists: { type: Array, default: () => [] },
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
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : 'Date to be announced');
const joinedYear = props.profile.joined ? new Date(props.profile.joined).getFullYear() : null;
</script>

<template>
    <Head :title="`${profile.name} — Gift Loft`" />

    <PublicLayout theme="light">
        <div class="pb-20" style="padding-top: calc(var(--nav-offset, 62px) + 2.5rem)">
            <div class="mx-auto max-w-5xl px-6 sm:px-10">
                <!-- Profile header -->
                <div class="flex flex-col items-center gap-5 text-center sm:flex-row sm:gap-7 sm:text-left">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-4xl font-black text-white shadow-lg shadow-indigo-500/25">
                        {{ profile.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-3xl font-black tracking-tight text-neutral-950 sm:text-4xl">{{ profile.name }}</h1>
                        <p v-if="profile.username" class="mt-1 text-sm font-semibold text-indigo-600">@{{ profile.username }}</p>
                        <p class="mt-2 text-sm text-neutral-500">
                            Celebrating on Gift Loft<span v-if="joinedYear"> since {{ joinedYear }}</span>
                            · {{ events.length }} {{ events.length === 1 ? 'event' : 'events' }}
                            · {{ wishlists.length }} {{ wishlists.length === 1 ? 'wishlist' : 'wishlists' }}
                        </p>
                    </div>
                </div>

                <!-- Events -->
                <div v-if="events.length" class="mt-14">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-neutral-500">Celebrations</h2>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <Link v-for="event in events" :key="event.id" :href="route('public.event', event.share_code)"
                            class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative h-28 overflow-hidden bg-gradient-to-br" :class="gradient(event.type)">
                                <img v-if="event.cover_photo_url" :src="event.cover_photo_url" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            </div>
                            <div class="p-5">
                                <p class="truncate text-sm font-bold text-neutral-900">{{ event.title }}</p>
                                <p class="mt-1 text-xs text-neutral-500">{{ fmtDate(event.starts_at) }}<span v-if="event.location"> · {{ event.location }}</span></p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Wishlists -->
                <div v-if="wishlists.length" class="mt-12">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-neutral-500">Wishlists</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <Link v-for="w in wishlists" :key="w.id" :href="route('public.wishlist', w.slug)"
                            class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-xl">🎁</div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-neutral-900">{{ w.name }}</p>
                                <p class="mt-0.5 text-xs text-neutral-500">{{ w.items_count }} {{ w.items_count === 1 ? 'item' : 'items' }}</p>
                            </div>
                            <span class="text-xs font-semibold text-indigo-600 opacity-0 transition group-hover:opacity-100">View →</span>
                        </Link>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="!events.length && !wishlists.length" class="mt-14 rounded-[1.75rem] border border-dashed border-neutral-300 bg-white px-8 py-16 text-center">
                    <p class="text-3xl">🌱</p>
                    <p class="mt-3 text-base font-bold text-neutral-900">Nothing public here yet</p>
                    <p class="mt-2 text-sm text-neutral-500">{{ profile.name }} hasn't shared any events or wishlists.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
