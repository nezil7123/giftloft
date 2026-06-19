<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    events: { type: Array, default: () => [] },
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

const hasEvents = computed(() => props.events.length > 0);
</script>

<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-neutral-900">My Events</h2>
                    <p class="text-sm text-neutral-500">Manage your event pages and gift registries.</p>
                </div>
                <Link
                    :href="route('events.create')"
                    class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Create Event
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div v-if="hasEvents" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="event in events"
                        :key="event.id"
                        :href="route('events.show', event.id)"
                        class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div
                            class="relative h-28 bg-gradient-to-br"
                            :class="gradient(event.type)"
                            :style="event.cover_photo_url ? `background-image:url('${event.cover_photo_url}');background-size:cover;background-position:center` : ''"
                        >
                            <div class="absolute inset-0 bg-black/10"></div>
                            <span
                                class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide"
                                :class="event.status === 'published' ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-neutral-900'"
                            >{{ event.status }}</span>
                        </div>
                        <div class="p-5">
                            <h3 class="text-base font-bold text-neutral-900 group-hover:text-indigo-600">{{ event.title }}</h3>
                            <p class="mt-1 text-xs uppercase tracking-wide text-neutral-400">{{ (event.type || 'event').replace('_', ' ') }}</p>
                            <p class="mt-3 text-sm text-neutral-500">{{ fmtDate(event.starts_at) }}</p>
                            <div class="mt-4 flex gap-2 text-xs">
                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700">{{ event.wishlists_count ?? 0 }} wishlists</span>
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 font-semibold text-neutral-600">{{ event.gifts_count ?? 0 }} gifts</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <p class="mt-5 text-lg font-bold text-neutral-900">No events yet</p>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-neutral-500">Create your first event to start building invitations, wishlists, and guest interactions.</p>
                    <Link :href="route('events.create')" class="mt-6 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Create an Event
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
