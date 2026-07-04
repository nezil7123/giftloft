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

// Two counter-scrolling film strips built from the gallery.
const stripA = computed(() => gallery.value.filter((_, i) => i % 2 === 0));
const stripB = computed(() => gallery.value.filter((_, i) => i % 2 === 1));
</script>

<template>
    <div ref="root" class="bg-[#fdf6ec] text-stone-800">
        <!-- HERO — the setting sun -->
        <section class="gl-grain relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-24">
            <!-- Sun disc -->
            <!-- Centered with margins, not translate — GSAP's parallax transform would overwrite a translate-x class. -->
            <div data-fx="parallax" data-speed="0.5" class="pointer-events-none absolute left-1/2 top-[8%] -ml-36 sm:-ml-48">
                <div class="relative h-72 w-72 sm:h-96 sm:w-96">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-b from-amber-300 via-orange-400 to-rose-400 opacity-90"></div>
                    <div class="absolute -inset-8 rounded-full bg-orange-300/40 blur-3xl"></div>
                    <svg data-fx="rotate" data-turns="0.35" viewBox="0 0 200 200" class="absolute -inset-14 text-orange-400/50">
                        <g stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                            <line v-for="n in 24" :key="n" x1="100" y1="4" x2="100" y2="16" :transform="`rotate(${n * 15} 100 100)`" />
                        </g>
                    </svg>
                </div>
            </div>
            <!-- Horizon haze -->
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#fdf6ec] via-[#fdf6ec]/90 to-transparent"></div>

            <div data-fx="hero-exit" class="relative z-10 mt-24 text-center">
                <p class="gl-enter text-xs font-bold uppercase tracking-[0.5em] text-orange-700/80">{{ typeLabel }}</p>
                <h1 data-fx="chars" class="gl-display mt-6 font-serif font-medium text-stone-900">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-5 font-serif text-2xl italic text-orange-800/80">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-4 text-sm font-semibold uppercase tracking-[0.3em] text-stone-500">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-10 inline-flex items-center gap-2">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex h-20 w-20 flex-col items-center justify-center rounded-full border border-orange-300/60 bg-white/60 backdrop-blur sm:h-24 sm:w-24">
                        <span class="font-serif text-2xl tabular-nums text-orange-900 sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-stone-500">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-8 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem]">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.5em] text-orange-700/80">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-stone-700 sm:text-3xl">{{ event.description }}</p>
                <div class="gl-grow mx-auto mt-10 h-px w-28 bg-gradient-to-r from-transparent via-orange-400 to-transparent"></div>
            </div>
        </section>

        <!-- DETAILS — boarding-pass tickets -->
        <section class="px-6 pb-12 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="gl-tilt relative overflow-hidden rounded-3xl bg-white p-8 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100">
                    <div class="absolute -right-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-[#fdf6ec]"></div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-orange-500">When</p>
                    <p class="mt-3 font-serif text-2xl text-stone-900">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="gl-tilt relative overflow-hidden rounded-3xl bg-white p-8 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100">
                    <div class="absolute -left-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-[#fdf6ec]"></div>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-orange-500">Where</p>
                    <p class="mt-3 font-serif text-2xl text-stone-900">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl bg-white p-8 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100 sm:p-12">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.5em] text-orange-700/80">As the sun goes down</p>
                <div class="mt-8">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="group flex items-baseline gap-6 border-t border-dashed border-orange-200 py-6">
                        <span class="relative flex h-3 w-3 shrink-0 self-center">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-60 group-hover:animate-ping"></span>
                            <span class="relative inline-flex h-3 w-3 rounded-full bg-gradient-to-br from-amber-400 to-rose-400"></span>
                        </span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-stone-900">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-stone-500">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm font-bold uppercase tracking-widest text-orange-600">{{ s.time }}</span>
                    </div>
                </div>
            </div>

            <div data-fx="batch" class="mx-auto mt-5 grid max-w-5xl gap-5 sm:grid-cols-2">
                <div v-if="dressCode" class="rounded-3xl bg-gradient-to-br from-amber-100 to-orange-100 p-8 text-center ring-1 ring-orange-200/60">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-orange-600">Dress code</p>
                    <p class="mt-3 font-serif text-2xl text-stone-900">{{ dressCode }}</p>
                </div>
                <div v-for="(f,i) in faqs" :key="i" class="rounded-3xl bg-white p-8 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100">
                    <p class="font-serif text-xl text-stone-900">{{ f.question }}</p>
                    <p class="mt-2 leading-7 text-stone-500">{{ f.answer }}</p>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="relative overflow-hidden px-6 py-24 sm:px-10">
            <div data-fx="parallax" data-speed="0.3" class="pointer-events-none absolute -right-32 top-16 h-80 w-80 rounded-full bg-gradient-to-b from-amber-300/50 to-rose-300/40 blur-3xl"></div>
            <div class="relative mx-auto max-w-5xl text-center">
                <p data-reveal class="text-xs font-bold uppercase tracking-[0.5em] text-orange-700/80">Where we'll gather</p>
                <h2 data-fx="chars" class="mt-4 font-serif text-4xl text-stone-900 sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl font-serif text-xl font-light italic leading-8 text-stone-600">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2.5rem]">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 text-left sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl bg-white p-7 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-orange-500">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-stone-600">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl bg-white p-7 shadow-lg shadow-orange-200/40 ring-1 ring-orange-100">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-orange-500">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-stone-600">{{ stay }}</p>
                    </div>
                </div>
                <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="gl-btn mt-8 inline-flex rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-8 py-3.5 text-sm font-bold text-white transition hover:scale-105">Open in Maps ↗</a>
            </div>
        </section>

        <!-- GALLERY — counter-scrolling film strips -->
        <section v-if="gallery.length" class="overflow-hidden py-16">
            <p data-reveal class="text-center text-xs font-bold uppercase tracking-[0.5em] text-orange-700/80">Golden moments</p>
            <div class="mt-10 space-y-4">
                <div class="gl-marquee">
                    <span v-for="g in 2" :key="g" class="flex gap-4 pr-4">
                        <span v-for="(p,i) in (stripA.length ? stripA : gallery)" :key="i" class="gl-photo block w-72 shrink-0 overflow-hidden rounded-2xl border-4 border-white shadow-lg shadow-orange-200/50 sm:w-96">
                            <img :src="p" alt="" loading="lazy" class="aspect-[3/2] w-full object-cover" />
                        </span>
                    </span>
                </div>
                <div v-if="stripB.length" class="gl-marquee gl-marquee-rev">
                    <span v-for="g in 2" :key="g" class="flex gap-4 pr-4">
                        <span v-for="(p,i) in stripB" :key="i" class="gl-photo block w-72 shrink-0 overflow-hidden rounded-2xl border-4 border-white shadow-lg shadow-orange-200/50 sm:w-96">
                            <img :src="p" alt="" loading="lazy" class="aspect-[3/2] w-full object-cover" />
                        </span>
                    </span>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-24 sm:px-10">
            <div data-reveal="zoom" class="gl-grain relative mx-auto max-w-5xl overflow-hidden rounded-[3rem] bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 px-8 py-20 text-center text-white">
                <div class="gl-pan pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.25),transparent_55%)]"></div>
                <h3 class="relative font-serif text-4xl sm:text-5xl">Warm their heart</h3>
                <p class="relative mx-auto mt-4 max-w-md text-white/85">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-bold text-orange-600 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs font-bold uppercase tracking-[0.3em] text-stone-400">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
