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
    <div ref="root" class="bg-[#f2f8f9] text-[#1c3a44]">
        <!-- HERO -->
        <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-24 text-center">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-sky-100 via-[#f2f8f9] to-cyan-50"></div>
            <div data-fx="parallax" data-speed="0.6" class="pointer-events-none absolute -bottom-24 left-0 h-72 w-[140%] rounded-[100%] bg-cyan-200/40 blur-3xl"></div>
            <div data-fx="parallax" data-speed="0.4" class="pointer-events-none absolute -bottom-32 left-[-10%] h-80 w-[130%] rounded-[100%] bg-sky-300/30 blur-3xl"></div>

            <div data-fx="hero-exit" class="relative z-10">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-cyan-700/80">{{ typeLabel }}</p>
                <h1 data-fx="chars" class="gl-display mt-8 font-serif font-medium leading-[0.9]">
                    <span class="bg-gradient-to-r from-cyan-700 via-sky-600 to-teal-600 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-lg font-light tracking-wide text-sky-800/80 sm:text-2xl">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.35em] text-[#5a7a82]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-3">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl bg-white/60 ring-1 ring-cyan-500/15 backdrop-blur-sm sm:h-24 sm:w-24">
                        <span class="text-2xl font-bold tabular-nums text-cyan-800 sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[9px] uppercase tracking-[0.25em] text-[#5a7a82]">{{ u.l }}</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.4em] text-cyan-700/50">Scroll</div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-6 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-[2rem] shadow-xl shadow-cyan-900/5">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-cyan-700/80">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-[#274d57] sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="rounded-3xl bg-white/70 p-8 ring-1 ring-cyan-500/15 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700/80">When</p>
                    <p class="mt-3 font-serif text-2xl text-[#1c3a44]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="rounded-3xl bg-white/70 p-8 ring-1 ring-cyan-500/15 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700/80">Where</p>
                    <p class="mt-3 font-serif text-2xl text-[#1c3a44]">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl bg-white/70 p-8 ring-1 ring-cyan-500/15 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-cyan-700/80">The plan</p>
                <div class="mt-6">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t border-cyan-500/15 py-5">
                        <span class="w-24 shrink-0 text-sm uppercase tracking-widest text-cyan-700/70">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-[#1c3a44]">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-[#5a7a82]">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl bg-white/70 p-8 text-center ring-1 ring-cyan-500/15">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700/80">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-[#1c3a44]">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl bg-white/70 p-8 ring-1 ring-cyan-500/15 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-cyan-700/80">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-[#1c3a44]">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-[#4a6a72]">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-cyan-700/80">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-[#1c3a44] sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-[#4a6a72]">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2rem] shadow-xl shadow-cyan-900/5">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl bg-white/70 p-7 ring-1 ring-cyan-500/15">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700/80">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-[#3a5a62]">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl bg-white/70 p-7 ring-1 ring-cyan-500/15">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-700/80">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-[#3a5a62]">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-cyan-600/40 px-7 py-3 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-500/10">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-cyan-700/80">Moments</p>
            <div class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.1 + (i % 3) * 0.1).toFixed(2)" class="gl-photo group overflow-hidden rounded-2xl">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-cyan-600 to-sky-700 px-8 py-20 text-center text-white">
                <h3 class="font-serif text-4xl sm:text-5xl">Wishlist</h3>
                <p class="mx-auto mt-4 max-w-md text-cyan-50/90">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-semibold text-cyan-800 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-[#5a7a82]">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
