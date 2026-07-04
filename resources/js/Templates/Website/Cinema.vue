<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, venuePhoto, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);

const marquee = computed(() => [(hosts.value || props.event.title).toUpperCase(), '—', fmtDate(props.event.starts_at), '—']);
const acts = ['ACT I', 'ACT II', 'ACT III', 'ACT IV', 'ACT V'];
</script>

<template>
    <div ref="root" class="bg-neutral-950 text-neutral-100">
        <!-- HERO — one-sheet poster -->
        <section class="gl-grain relative flex min-h-screen flex-col justify-between overflow-hidden">
            <div class="absolute inset-0">
                <img v-if="coverPhoto" :src="coverPhoto" alt="" class="gl-ken h-full w-full object-cover opacity-40 grayscale" />
                <div class="absolute inset-0 bg-gradient-to-b from-neutral-950/80 via-transparent to-neutral-950"></div>
            </div>

            <div class="relative z-10 flex items-center justify-between px-6 pt-8 text-[10px] uppercase tracking-[0.4em] text-neutral-400 sm:px-12">
                <span>Gift Loft présente</span>
                <span>{{ typeLabel }}</span>
            </div>

            <div data-fx="hero-exit" class="relative z-10 px-6 pb-24 sm:px-12">
                <p class="gl-enter text-xs uppercase tracking-[0.5em] text-neutral-400">A once-in-a-lifetime feature</p>
                <h1 data-fx="chars" class="gl-display mt-6 font-serif uppercase text-white">{{ hosts || event.title }}</h1>
                <div class="mt-8 flex flex-wrap items-end justify-between gap-6">
                    <p v-if="tagline" class="gl-enter gl-d2 max-w-md font-serif text-xl italic text-neutral-300 sm:text-2xl">“{{ tagline }}”</p>
                    <div v-if="countdown" class="gl-enter gl-d3 flex items-baseline gap-4 tabular-nums">
                        <span v-for="u in [{v:countdown.days,l:'D'},{v:countdown.hours,l:'H'},{v:countdown.mins,l:'M'},{v:countdown.secs,l:'S'}]" :key="u.l" class="flex items-baseline gap-1">
                            <span class="text-4xl font-bold text-white sm:text-5xl">{{ String(u.v).padStart(2,'0') }}</span>
                            <span class="text-xs text-neutral-500">{{ u.l }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden border-y border-neutral-800 py-6">
            <div class="gl-marquee">
                <span v-for="g in 3" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-8 font-serif text-3xl tracking-tight" :class="m === '—' ? 'text-neutral-600' : 'gl-outline text-white/85'">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- SYNOPSIS -->
        <section v-if="event.description" class="px-6 py-32 sm:px-12">
            <div class="mx-auto grid max-w-6xl gap-10 sm:grid-cols-[auto_1fr]">
                <p data-reveal class="text-xs uppercase tracking-[0.5em] text-neutral-500 sm:rotate-180 sm:[writing-mode:vertical-rl]">Synopsis</p>
                <p data-fx="words" class="whitespace-pre-line font-serif text-3xl font-light leading-snug text-neutral-100 sm:text-4xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- SCREENING DETAILS -->
        <section class="border-t border-neutral-800 px-6 py-20 sm:px-12">
            <div data-fx="batch" class="mx-auto grid max-w-6xl gap-px overflow-hidden rounded-none sm:grid-cols-3">
                <div class="border border-neutral-800 p-8">
                    <p class="text-[10px] uppercase tracking-[0.4em] text-neutral-500">Premiere</p>
                    <p class="mt-4 font-serif text-2xl text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="border border-neutral-800 p-8">
                    <p class="text-[10px] uppercase tracking-[0.4em] text-neutral-500">Location</p>
                    <p class="mt-4 font-serif text-2xl text-white">{{ location || 'To be announced' }}</p>
                </div>
                <div class="border border-neutral-800 p-8">
                    <p class="text-[10px] uppercase tracking-[0.4em] text-neutral-500">Dress code</p>
                    <p class="mt-4 font-serif text-2xl text-white">{{ dressCode || 'Come as you are' }}</p>
                </div>
            </div>

            <!-- Programme as acts -->
            <div v-if="schedule.length" class="mx-auto mt-24 max-w-6xl">
                <p data-reveal class="text-xs uppercase tracking-[0.5em] text-neutral-500">Programme</p>
                <div class="mt-10">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="group grid items-baseline gap-4 border-t border-neutral-800 py-8 transition hover:bg-neutral-900/50 sm:grid-cols-[8rem_1fr_auto]">
                        <span class="font-serif text-sm italic text-neutral-500">{{ acts[i] ?? `ACT ${i+1}` }}</span>
                        <div>
                            <p class="font-serif text-3xl uppercase text-white sm:text-4xl">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-2 text-sm text-neutral-400">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm tabular-nums tracking-[0.2em] text-neutral-400">{{ s.time }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALLERY — pinned horizontal reel -->
        <section v-if="gallery.length" data-fx="hscroll" class="relative overflow-hidden border-t border-neutral-800">
            <div class="flex h-screen items-center">
                <div data-fx-track class="flex items-center gap-6 pl-6 pr-[12vw] sm:pl-12">
                    <div class="w-64 shrink-0">
                        <p class="text-xs uppercase tracking-[0.5em] text-neutral-500">The reel</p>
                        <p class="mt-4 font-serif text-4xl text-white">Stills from our story</p>
                        <p class="mt-4 text-xs text-neutral-500">Keep scrolling →</p>
                    </div>
                    <figure v-for="(p,i) in gallery" :key="i" class="gl-photo group relative w-[70vw] shrink-0 sm:w-[42vw]">
                        <img :src="p" alt="" loading="lazy" class="aspect-[3/2] w-full object-cover grayscale transition duration-700 group-hover:grayscale-0" />
                        <figcaption class="mt-3 flex justify-between text-[10px] uppercase tracking-[0.3em] text-neutral-500">
                            <span>Frame {{ String(i+1).padStart(2,'0') }}</span>
                            <span>{{ hosts || event.title }}</span>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="border-t border-neutral-800 px-6 py-28 sm:px-12">
            <div class="mx-auto max-w-6xl">
                <p data-reveal class="text-xs uppercase tracking-[0.5em] text-neutral-500">On location</p>
                <h2 data-fx="chars" class="mt-6 font-serif text-5xl uppercase text-white sm:text-6xl">{{ event.venue || location }}</h2>
                <div class="mt-12 grid gap-10 sm:grid-cols-2">
                    <div v-if="venuePhoto" data-fx="img-reveal" class="overflow-hidden">
                        <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover grayscale" />
                    </div>
                    <div class="space-y-8">
                        <p v-if="venueNote" data-fx="words" class="font-serif text-2xl font-light leading-relaxed text-neutral-200">{{ venueNote }}</p>
                        <div v-if="travel" data-reveal>
                            <p class="text-[10px] uppercase tracking-[0.4em] text-neutral-500">Getting there</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-300">{{ travel }}</p>
                        </div>
                        <div v-if="stay" data-reveal>
                            <p class="text-[10px] uppercase tracking-[0.4em] text-neutral-500">Where to stay</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-300">{{ stay }}</p>
                        </div>
                        <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" data-reveal class="gl-btn inline-flex border border-neutral-600 px-7 py-3 text-xs font-semibold uppercase tracking-[0.25em] text-white transition hover:bg-white hover:text-neutral-950">Open in Maps</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section v-if="faqs.length" class="border-t border-neutral-800 px-6 py-24 sm:px-12">
            <div class="mx-auto max-w-4xl">
                <p data-reveal class="text-xs uppercase tracking-[0.5em] text-neutral-500">Notes for the audience</p>
                <div class="mt-10 space-y-10">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-2xl text-white">{{ f.question }}</p>
                        <p class="mt-2 leading-7 text-neutral-400">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA — end credits -->
        <section class="gl-grain border-t border-neutral-800 px-6 py-32 text-center sm:px-12">
            <p data-reveal class="text-xs uppercase tracking-[0.5em] text-neutral-500">The end… is a beginning</p>
            <h3 data-fx="chars" class="gl-display-sm mt-6 font-serif uppercase text-white">Send a gift</h3>
            <p data-reveal class="mx-auto mt-5 max-w-md text-neutral-400">{{ rsvpNote || 'Browse our wishlist and be part of the story.' }}</p>
            <Link :href="registryUrl" data-reveal class="gl-btn mt-10 inline-flex items-center justify-center bg-white px-10 py-4 text-xs font-semibold uppercase tracking-[0.3em] text-neutral-950 transition hover:scale-105">View the registry</Link>
            <p class="mt-16 text-[10px] uppercase tracking-[0.4em] text-neutral-600">{{ event.user?.name }} · A Gift Loft production</p>
        </section>
    </div>
</template>
