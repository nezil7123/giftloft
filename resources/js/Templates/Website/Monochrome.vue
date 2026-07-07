<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, venuePhoto, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);
</script>

<template>
    <div ref="root" class="bg-white text-neutral-950">
        <!-- HERO -->
        <section class="gl-gridlines relative flex min-h-screen flex-col justify-between overflow-hidden px-6 py-16 sm:px-10">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">
                <span class="gl-enter">{{ typeLabel }}</span>
                <span class="gl-enter gl-d1">{{ fmtDate(event.starts_at) }}</span>
            </div>

            <div data-fx="hero-exit">
                <h1 data-fx="chars" class="text-[16vw] font-black uppercase leading-[0.82] tracking-tighter sm:text-[13vw]">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 max-w-xl text-lg font-medium text-neutral-600 sm:text-2xl">{{ tagline }}</p>
            </div>

            <div class="flex flex-wrap items-end justify-between gap-6 border-t-2 border-neutral-950 pt-6">
                <div v-if="location" class="gl-enter gl-d3 text-sm font-bold uppercase tracking-[0.2em]">{{ location }}</div>
                <div v-if="countdown" class="gl-enter gl-d4 flex items-end gap-6">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l">
                        <div class="text-4xl font-black tabular-nums leading-none sm:text-5xl">{{ String(u.v).padStart(2,'0') }}</div>
                        <div class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">{{ u.l }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="border-y-2 border-neutral-950">
            <div data-fx="img-reveal" class="overflow-hidden">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover grayscale transition duration-700 hover:grayscale-0" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto grid max-w-6xl gap-8 sm:grid-cols-[0.3fr_1fr]">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Story / 01</p>
                <p data-fx="words" class="max-w-3xl whitespace-pre-line text-3xl font-medium leading-tight tracking-tight sm:text-4xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="border-t-2 border-neutral-950 px-6 py-20 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-6xl gap-0 sm:grid-cols-2">
                <div class="border-neutral-950 p-8 sm:border-r-2">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">When</p>
                    <p class="mt-3 text-2xl font-bold">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Where</p>
                    <p class="mt-3 text-2xl font-bold">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-16 max-w-6xl">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Schedule / 02</p>
                <div class="mt-6">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t-2 border-neutral-950 py-6">
                        <span class="w-24 shrink-0 text-sm font-bold uppercase tracking-widest">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="text-2xl font-bold">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-neutral-500">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-16 max-w-6xl border-t-2 border-neutral-950 pt-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Dress code</p>
                <p class="mt-3 text-3xl font-black uppercase tracking-tight">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-16 max-w-6xl">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Info / 03</p>
                <div class="mt-6 grid gap-8 sm:grid-cols-2">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal class="border-t-2 border-neutral-950 pt-4">
                        <p class="text-xl font-bold">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-neutral-600">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="border-t-2 border-neutral-950 px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-6xl">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Venue / 04</p>
                <h2 data-fx="chars" class="mt-3 text-5xl font-black uppercase tracking-tighter sm:text-7xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mt-6 max-w-2xl text-xl font-medium leading-8 text-neutral-600">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mt-10 overflow-hidden border-2 border-neutral-950">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/9] w-full object-cover grayscale" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-0 sm:grid-cols-2">
                    <div v-if="travel" class="border-neutral-950 p-7 sm:border-r-2">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-700">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="p-7">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-500">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-700">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="inline-flex border-2 border-neutral-950 px-7 py-3 text-sm font-bold uppercase tracking-widest transition hover:bg-neutral-950 hover:text-white">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="border-t-2 border-neutral-950">
            <div class="grid grid-cols-2 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" class="gl-photo group overflow-hidden border-neutral-950 [&:not(:nth-child(3n))]:border-r-2 [&:nth-child(n+4)]:border-t-2">
                    <img :src="p" alt="" loading="lazy" class="aspect-square w-full object-cover grayscale transition duration-700 group-hover:grayscale-0 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="border-t-2 border-neutral-950 bg-neutral-950 px-6 py-28 text-white sm:px-10">
            <div data-reveal="zoom" class="mx-auto max-w-6xl text-center">
                <h3 class="text-6xl font-black uppercase tracking-tighter sm:text-8xl">Wishlist</h3>
                <p class="mx-auto mt-4 max-w-md text-neutral-400">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="mt-9 inline-flex items-center justify-center bg-white px-9 py-4 text-sm font-bold uppercase tracking-widest text-neutral-950 transition hover:scale-105">View the registry</Link>
                <p class="mt-14 text-xs font-bold uppercase tracking-[0.3em] text-neutral-600">{{ event.user?.name }} · Gift Loft</p>
            </div>
        </section>
    </div>
</template>
