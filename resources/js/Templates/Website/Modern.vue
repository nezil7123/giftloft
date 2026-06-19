<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-white text-neutral-950">
        <!-- Hero: split, type-led, minimal -->
        <section class="mx-auto grid min-h-[88vh] max-w-7xl items-center gap-12 px-6 pt-32 pb-16 sm:px-10 lg:grid-cols-2 lg:gap-20">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.5em] text-neutral-400">{{ typeLabel }}</p>
                <h1 class="mt-6 text-6xl font-extrabold leading-[0.95] tracking-tighter text-neutral-950 sm:text-7xl lg:text-8xl">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="mt-6 max-w-md text-lg text-neutral-500">{{ tagline }}</p>

                <div class="mt-10 h-px w-full bg-neutral-200"></div>
                <div class="mt-6 flex flex-wrap gap-x-12 gap-y-4 text-sm">
                    <div v-if="event.starts_at">
                        <p class="text-xs uppercase tracking-widest text-neutral-400">When</p>
                        <p class="mt-1 font-semibold">{{ fmtFull(event.starts_at) }}</p>
                    </div>
                    <div v-if="location">
                        <p class="text-xs uppercase tracking-widest text-neutral-400">Where</p>
                        <p class="mt-1 font-semibold">{{ location }}</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="aspect-[4/5] overflow-hidden rounded-3xl bg-neutral-100">
                    <img v-if="event.cover_photo_url" :src="event.cover_photo_url" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-neutral-200 to-neutral-300 text-7xl">✦</div>
                </div>
                <div v-if="countdown" class="absolute -bottom-6 left-1/2 flex -translate-x-1/2 gap-6 rounded-2xl bg-neutral-950 px-8 py-4 text-white shadow-xl">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l" class="text-center">
                        <p class="text-2xl font-extrabold">{{ String(u.v).padStart(2,'0') }}</p>
                        <p class="text-[10px] uppercase tracking-widest text-white/50">{{ u.l }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-3xl space-y-16 px-6 py-20 sm:px-10">
            <div v-if="event.description">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">About</p>
                <p class="mt-5 whitespace-pre-line text-2xl font-light leading-relaxed text-neutral-800">{{ event.description }}</p>
            </div>

            <div v-if="schedule.length">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Schedule</p>
                <div class="mt-6 divide-y divide-neutral-200">
                    <div v-for="(s,i) in schedule" :key="i" class="flex items-baseline justify-between gap-6 py-4">
                        <div>
                            <p class="font-semibold text-neutral-950">{{ s.title }}</p>
                            <p v-if="s.detail" class="text-sm text-neutral-500">{{ s.detail }}</p>
                        </div>
                        <p class="shrink-0 text-sm font-medium text-neutral-400">{{ s.time }}</p>
                    </div>
                </div>
            </div>

            <div v-if="dressCode">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Dress code</p>
                <p class="mt-4 text-xl text-neutral-800">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">FAQ</p>
                <div class="mt-6 divide-y divide-neutral-200">
                    <div v-for="(f,i) in faqs" :key="i" class="py-5">
                        <p class="font-semibold text-neutral-950">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-neutral-600">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-neutral-200 pt-12 text-center">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Registry</p>
                <p v-if="rsvpNote" class="mx-auto mt-3 max-w-md text-neutral-600">{{ rsvpNote }}</p>
                <Link :href="registryUrl" class="mt-6 inline-flex items-center justify-center rounded-full bg-neutral-950 px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-neutral-800">View the registry →</Link>
                <p class="mt-10 text-sm text-neutral-400">Hosted by {{ event.user?.name }} · Powered by Gift Loft</p>
            </div>
        </section>
    </div>
</template>
