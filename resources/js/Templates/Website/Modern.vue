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

const marquee = computed(() => [hosts.value || props.event.title, '—', fmtDate(props.event.starts_at), '—', location.value || 'Save the date', '—']);
const nameWords = computed(() => (hosts.value || props.event.title || '').split(' '));
const strip = computed(() => [coverPhoto.value, ...gallery.value].filter(Boolean));
</script>

<template>
    <div ref="root" class="bg-[#f4f3f0] text-neutral-950">
        <!-- HERO — kinetic type × photo column -->
        <section class="gl-grain relative min-h-screen overflow-hidden">
            <div class="mx-auto grid min-h-screen max-w-7xl lg:grid-cols-[1.15fr_0.85fr]">
                <!-- Type side -->
                <div class="flex flex-col justify-between px-6 pb-10 pt-28 sm:px-10">
                    <div class="flex items-start justify-between">
                        <p class="gl-enter text-xs font-bold uppercase tracking-[0.4em] text-neutral-500">{{ typeLabel }}</p>
                        <p class="gl-enter gl-d1 max-w-[10rem] text-right text-xs font-medium leading-5 text-neutral-500">{{ fmtFull(event.starts_at) }}</p>
                    </div>

                    <div data-fx="hero-exit" class="py-10">
                        <h1>
                            <span v-for="(w,i) in nameWords" :key="i" class="block overflow-hidden">
                                <span class="gl-clip gl-display block font-black uppercase text-neutral-950" :style="`animation-delay:${i * 0.12}s`">{{ w }}</span>
                            </span>
                        </h1>
                        <p v-if="tagline" class="gl-enter gl-d2 mt-6 max-w-xl text-xl text-neutral-600 sm:text-2xl">{{ tagline }}</p>
                    </div>

                    <div class="flex flex-col gap-8 border-t-2 border-neutral-950 py-8 sm:flex-row sm:items-end sm:justify-between">
                        <div v-if="location" class="gl-enter gl-d3">
                            <p class="text-[10px] uppercase tracking-[0.3em] text-neutral-400">Location</p>
                            <p class="mt-1 text-lg font-bold">{{ location }}</p>
                        </div>
                        <div v-if="countdown" class="gl-enter gl-d3 flex gap-6">
                            <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l">
                                <p class="text-3xl font-black tabular-nums">{{ String(u.v).padStart(2,'0') }}</p>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-neutral-400">{{ u.l }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photo side -->
                <div class="relative hidden overflow-hidden lg:block">
                    <img v-if="coverPhoto" :src="coverPhoto" alt="" class="gl-ken absolute inset-0 h-full w-full object-cover" />
                    <div v-else class="gl-mesh-warm gl-mesh-anim absolute inset-0 flex items-center justify-center text-7xl">✦</div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#f4f3f0] via-transparent to-transparent"></div>
                    <p class="absolute bottom-8 right-8 rotate-90 text-[10px] uppercase tracking-[0.4em] text-white/80 mix-blend-difference">{{ fmtDate(event.starts_at) }}</p>
                </div>

                <!-- Mobile photo strip -->
                <div v-if="coverPhoto" class="gl-photo mx-6 mb-10 overflow-hidden rounded-2xl lg:hidden">
                    <img :src="coverPhoto" alt="" class="aspect-[16/10] w-full object-cover" />
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden border-y-2 border-neutral-950 bg-neutral-950 py-4 text-[#f4f3f0]">
            <div class="gl-marquee gl-marquee-fast">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 text-3xl font-black uppercase tracking-tight" :class="{ 'gl-outline': m !== '—' && i % 2 === 0 }">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-[0.4fr_1fr]">
                <p data-reveal="left" class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">About</p>
                <p data-reveal="right" class="whitespace-pre-line text-3xl font-light leading-snug text-neutral-900 sm:text-4xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DIPTYCH — two offset photos, opposing parallax -->
        <section v-if="gallery.length >= 2" class="overflow-hidden px-6 pb-6 sm:px-10">
            <div class="mx-auto grid max-w-6xl grid-cols-2 items-start gap-4 sm:gap-6">
                <div class="overflow-hidden rounded-2xl">
                    <img :src="gallery[0]" alt="" loading="lazy" data-fx="parallax" data-speed="0.14" class="aspect-[3/4] w-full scale-125 object-cover" />
                </div>
                <div class="mt-12 overflow-hidden rounded-2xl sm:mt-20">
                    <img :src="gallery[1]" alt="" loading="lazy" data-fx="parallax" data-speed="-0.14" class="aspect-[3/4] w-full scale-125 object-cover" />
                </div>
            </div>
        </section>

        <!-- VENUE — editorial -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="border-t border-neutral-300 px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-12 lg:grid-cols-[0.4fr_1fr]">
                    <p data-reveal="left" class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">The venue</p>
                    <div>
                        <h2 data-fx="chars" class="text-4xl font-black uppercase tracking-tight sm:text-6xl">{{ event.venue || location }}</h2>
                        <p v-if="venueNote" data-reveal class="mt-6 max-w-2xl text-xl font-light leading-8 text-neutral-600">{{ venueNote }}</p>
                        <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-2 border-b-2 border-neutral-950 pb-1 text-sm font-bold uppercase tracking-widest transition-all hover:gap-4">Open in maps →</a>
                        <div v-if="venuePhoto" class="mt-8 overflow-hidden rounded-2xl">
                            <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/9] w-full object-cover" />
                        </div>
                        <div data-fx="batch" class="mt-10 grid gap-8 sm:grid-cols-2">
                            <div v-if="travel" class="border-t-2 border-neutral-950 pt-4">
                                <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-400">Getting there</p>
                                <p class="mt-2 leading-7 text-neutral-600">{{ travel }}</p>
                            </div>
                            <div v-if="stay" class="border-t-2 border-neutral-950 pt-4">
                                <p class="text-xs font-bold uppercase tracking-[0.3em] text-neutral-400">Where to stay</p>
                                <p class="mt-2 leading-7 text-neutral-600">{{ stay }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SCHEDULE big numbers -->
        <section v-if="schedule.length" class="border-t border-neutral-300 px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-6xl">
                <p data-reveal class="mb-10 text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Schedule</p>
                <div v-for="(s,i) in schedule" :key="i" data-reveal class="group grid grid-cols-[auto_1fr_auto] items-center gap-6 border-t border-neutral-300 py-8 transition hover:bg-white">
                    <span class="text-5xl font-black text-neutral-300 transition group-hover:text-neutral-950 sm:text-7xl">{{ String(i+1).padStart(2,'0') }}</span>
                    <div>
                        <p class="text-2xl font-bold sm:text-3xl">{{ s.title }}</p>
                        <p v-if="s.detail" class="mt-1 text-neutral-500">{{ s.detail }}</p>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-widest text-neutral-500">{{ s.time }}</span>
                </div>
            </div>
        </section>

        <!-- DRESS + FAQ -->
        <section class="px-6 pb-10 sm:px-10">
            <div class="mx-auto max-w-6xl space-y-5">
                <div v-if="dressCode" data-reveal class="rounded-3xl bg-white p-8 sm:p-10">
                    <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Dress code</p>
                    <p class="mt-3 text-3xl font-light">{{ dressCode }}</p>
                </div>
                <div v-if="faqs.length" data-reveal class="rounded-3xl bg-white p-8 sm:p-10">
                    <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">FAQ</p>
                    <div class="mt-6 divide-y divide-neutral-200">
                        <div v-for="(f,i) in faqs" :key="i" class="py-5">
                            <p class="text-lg font-bold">{{ f.question }}</p>
                            <p class="mt-1.5 leading-7 text-neutral-600">{{ f.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALLERY — pinned horizontal scroll -->
        <section v-if="strip.length" data-fx="hscroll" class="relative overflow-hidden border-t border-neutral-300 bg-[#f4f3f0]">
            <div class="flex h-screen items-center">
                <div data-fx-track class="flex items-center gap-8 pl-6 pr-24 will-change-transform sm:pl-10">
                    <div class="w-[72vw] shrink-0 sm:w-[38vw]">
                        <p class="text-xs font-bold uppercase tracking-[0.4em] text-neutral-400">Gallery</p>
                        <p class="mt-4 text-6xl font-black uppercase leading-[0.95] tracking-tight sm:text-8xl">Keep<br/>scroll<wbr/>ing<br/><span class="gl-outline">the story</span></p>
                        <p class="mt-6 text-sm text-neutral-500">{{ String(strip.length).padStart(2,'0') }} frames →</p>
                    </div>
                    <div v-for="(p,i) in strip" :key="i" class="relative w-[78vw] shrink-0 overflow-hidden rounded-3xl bg-neutral-200 sm:w-[44vw]">
                        <img :src="p" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover grayscale transition duration-700 hover:grayscale-0" />
                        <span class="absolute left-5 top-5 text-5xl font-black text-white mix-blend-difference">{{ String(i+1).padStart(2,'0') }}</span>
                    </div>
                    <div class="flex w-[60vw] shrink-0 flex-col items-center justify-center gap-6 sm:w-[30vw]">
                        <p class="text-center text-3xl font-black uppercase tracking-tight">See you<br/>there.</p>
                        <Link :href="registryUrl" class="gl-btn inline-flex rounded-full bg-neutral-950 px-8 py-4 text-sm font-bold uppercase tracking-widest text-white transition hover:scale-105">Send a gift →</Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-grain relative mx-auto max-w-6xl overflow-hidden rounded-[2.5rem] bg-neutral-950 px-8 py-20 text-center text-white">
                <div class="gl-aurora opacity-20"></div>
                <h3 class="gl-display-sm relative font-black uppercase">Send a gift</h3>
                <p v-if="rsvpNote" class="relative mx-auto mt-5 max-w-md text-white/60">{{ rsvpNote }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-bold uppercase tracking-widest text-neutral-950 transition hover:scale-105">View wishlist →</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-neutral-400">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
