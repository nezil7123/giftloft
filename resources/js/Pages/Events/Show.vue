<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    eventTypes: { type: Object, required: true },
});

const typeLabel = computed(() => props.eventTypes[props.event.type] ?? props.event.type ?? 'Event');

const dateRange = computed(() => {
    const fmt = (iso) =>
        new Date(iso).toLocaleString(undefined, {
            weekday: 'short', day: 'numeric', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
    if (!props.event.starts_at) return 'No date set';
    return props.event.ends_at ? `${fmt(props.event.starts_at)} → ${fmt(props.event.ends_at)}` : fmt(props.event.starts_at);
});

const shareUrl = computed(() =>
    props.event.share_code ? `${window.location.origin}/e/${props.event.share_code}` : null
);

const copied = ref(false);
const copyShare = async () => {
    if (!shareUrl.value) return;
    await navigator.clipboard.writeText(shareUrl.value);
    copied.value = true;
    setTimeout(() => (copied.value = false), 1800);
};

const destroy = () => {
    if (confirm('Delete this event? This cannot be undone.')) {
        router.delete(route('events.destroy', props.event.id));
    }
};
</script>

<template>
    <Head :title="event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Link :href="route('events.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← All events</Link>
                    <h2 class="mt-1 text-xl font-bold text-neutral-900">{{ event.title }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('events.design.edit', event.id)" class="rounded-full border border-neutral-300 px-5 py-2 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">Customize design</Link>
                    <Link :href="route('events.edit', event.id)" class="rounded-full bg-neutral-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700">Edit</Link>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Cover / hero -->
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <div
                        class="relative h-48 bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 sm:h-60"
                        :style="event.cover_photo_url ? `background-image:url('${event.cover_photo_url}');background-size:cover;background-position:center` : ''"
                    >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <div class="absolute bottom-4 left-5 flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-bold uppercase tracking-wide text-neutral-800">{{ typeLabel }}</span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide"
                                :class="event.status === 'published' ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-neutral-900'"
                            >{{ event.status }}</span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide"
                                :class="event.is_public ? 'bg-sky-500 text-white' : 'bg-neutral-700 text-white'"
                            >{{ event.is_public ? 'Public' : 'Private' }}</span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col gap-4 text-sm text-neutral-600 sm:flex-row sm:flex-wrap sm:gap-8">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ dateRange }}
                            </div>
                            <div v-if="event.venue || event.location" class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ [event.venue, event.location].filter(Boolean).join(', ') }}
                            </div>
                        </div>

                        <p v-if="event.description" class="mt-6 whitespace-pre-line text-sm leading-7 text-neutral-700">{{ event.description }}</p>
                        <p v-else class="mt-6 text-sm italic text-neutral-400">No description added yet.</p>
                    </div>
                </div>

                <!-- Share link -->
                <div v-if="event.is_public && shareUrl" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                    <p class="text-sm font-semibold text-neutral-800">Share your event page</p>
                    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                        <input :value="shareUrl" readonly class="w-full rounded-xl border-neutral-300 bg-neutral-50 text-sm text-neutral-600" />
                        <button @click="copyShare" class="shrink-0 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                            {{ copied ? 'Copied!' : 'Copy link' }}
                        </button>
                    </div>
                </div>

                <!-- Registry / gift summary -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-3xl font-extrabold text-neutral-900">{{ event.wishlists_count ?? 0 }}</p>
                        <p class="mt-1 text-sm text-neutral-500">Wishlists / registries</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-3xl font-extrabold text-neutral-900">{{ event.gifts_count ?? 0 }}</p>
                        <p class="mt-1 text-sm text-neutral-500">Gifts</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="destroy" class="text-sm font-semibold text-rose-600 transition hover:text-rose-700">Delete event</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
