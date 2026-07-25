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

// Soft marble veining rendered as layered radial gradients.
const marble = 'radial-gradient(120% 80% at 20% 10%, rgba(255,255,255,0.9), transparent 60%), radial-gradient(90% 60% at 85% 30%, rgba(214,203,178,0.25), transparent 55%), radial-gradient(100% 70% at 60% 100%, rgba(200,188,160,0.2), transparent 60%), #f7f5f0';
</script>

<template>
    <div ref="root" class="text-[#33302a]" :style="`background:${marble}`">
        <!-- HERO -->
        <section class="relative flex min-h-screen items-center justify-center px-6 py-24 text-center">
            <div data-fx="parallax" data-speed="0.25" class="pointer-events-none absolute left-1/2 top-16 h-64 w-64 -translate-x-1/2 rounded-full border border-amber-600/25"></div>
            <div data-fx="hero-exit" class="relative z-10">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-amber-700/80">{{ typeLabel }}</p>
                <div class="gl-enter gl-d1 mx-auto mt-8 flex items-center justify-center gap-3">
                    <span class="h-px w-14 bg-gradient-to-r from-transparent to-amber-600/50"></span>
                    <span class="text-amber-600">❖</span>
                    <span class="h-px w-14 bg-gradient-to-l from-transparent to-amber-600/50"></span>
                </div>
                <h1 class="mt-8 overflow-hidden">
                    <span class="gl-clip gl-display block font-serif font-medium leading-[0.9]">
                        <span class="gl-shimmer" style="--gl-shimmer-color:#9c7a3c">{{ hosts || event.title }}</span>
                    </span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-xl italic text-[#6a5c3e] sm:text-2xl">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.35em] text-[#8a7c5e]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-6 sm:gap-8">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="text-center">
                        <div class="font-serif text-4xl font-medium tabular-nums text-[#33302a] sm:text-5xl">{{ String(u.v).padStart(2,'0') }}</div>
                        <div class="mt-1 text-[10px] uppercase tracking-[0.3em] text-amber-700/60">{{ u.l }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-6 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-[2rem] ring-1 ring-amber-600/20 shadow-2xl shadow-amber-900/5">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <svg data-fx="draw" viewBox="0 0 120 60" fill="none" class="mx-auto mb-8 h-9 w-24 text-amber-700/60" aria-hidden="true">
                    <path d="M60 58 C60 40 60 30 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M60 20 C50 14 44 6 50 2 C56 -2 62 6 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M60 20 C70 14 76 6 70 2 C64 -2 58 6 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M60 30 C48 28 40 34 42 40 C44 46 54 44 60 36" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    <path d="M60 30 C72 28 80 34 78 40 C76 46 66 44 60 36" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-amber-700/80">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-[#43402e] sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <!-- Certificate-style panel -->
            <div data-fx="tilt3d" class="mx-auto max-w-xl rounded-[2rem] border border-amber-600/25 bg-white/50 p-10 text-center backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700/80">When</p>
                <p class="mt-3 font-serif text-2xl text-[#33302a]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                <div class="mx-auto my-6 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-amber-600/30"></span>
                    <span class="text-amber-600">❖</span>
                    <span class="h-px w-10 bg-amber-600/30"></span>
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700/80">Where</p>
                <p class="mt-3 font-serif text-2xl text-[#33302a]">{{ location || 'To be announced' }}</p>
            </div>

            <!-- Alternating center-line timeline -->
            <div v-if="schedule.length" class="mx-auto mt-16 max-w-3xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-amber-700/80">The day</p>
                <div class="relative mt-10">
                    <div class="absolute left-1/2 top-0 h-full w-px -translate-x-1/2 bg-amber-600/25"></div>
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="relative mb-10 grid grid-cols-2 gap-4 last:mb-0 sm:gap-8">
                        <div :class="i % 2 === 0 ? 'text-right' : ''">
                            <template v-if="i % 2 === 0">
                                <p class="font-serif text-lg text-[#33302a] sm:text-2xl">{{ s.title }}</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.3em] text-amber-700/70 sm:text-xs">{{ s.time }}</p>
                                <p v-if="s.detail" class="mt-1 text-xs text-[#8a7c5e] sm:text-sm">{{ s.detail }}</p>
                            </template>
                        </div>
                        <div>
                            <template v-if="i % 2 !== 0">
                                <p class="font-serif text-lg text-[#33302a] sm:text-2xl">{{ s.title }}</p>
                                <p class="mt-1 text-[10px] uppercase tracking-[0.3em] text-amber-700/70 sm:text-xs">{{ s.time }}</p>
                                <p v-if="s.detail" class="mt-1 text-xs text-[#8a7c5e] sm:text-sm">{{ s.detail }}</p>
                            </template>
                        </div>
                        <span class="absolute left-1/2 top-1 h-3 w-3 -translate-x-1/2 rounded-full bg-amber-600 ring-4 ring-[#f7f5f0]"></span>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl border border-amber-600/20 bg-white/50 p-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700/80">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-[#33302a]">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-amber-600/20 bg-white/50 p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-amber-700/80">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-[#33302a]">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-[#6a5c3e]">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-amber-700/80">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-[#33302a] sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-[#6a5c3e]">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2rem] ring-1 ring-amber-600/20">
                    <img :src="venuePhoto" alt="" loading="lazy" decoding="async" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl border border-amber-600/20 bg-white/50 p-7">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700/80">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-[#5a4d33]">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl border border-amber-600/20 bg-white/50 p-7">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700/80">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-[#5a4d33]">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-amber-700/40 px-7 py-3 text-sm font-semibold text-amber-800 transition hover:bg-amber-600/10">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY: organic masonry (CSS columns) -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-amber-700/80">Moments</p>
            <div class="mx-auto mt-10 max-w-5xl columns-2 gap-4 sm:columns-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.1 + (i % 3) * 0.1).toFixed(2)" class="gl-photo group mb-4 break-inside-avoid overflow-hidden rounded-2xl ring-1 ring-amber-600/15">
                    <img :src="p" alt="" loading="lazy" decoding="async" class="w-full object-cover transition duration-700 group-hover:scale-105" :style="`aspect-ratio:${i % 3 === 0 ? '3/4' : i % 3 === 1 ? '1/1' : '4/5'}`" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] border border-amber-600/25 bg-white/60 px-8 py-20 text-center backdrop-blur-sm">
                <h3 class="font-serif text-4xl text-[#33302a] sm:text-5xl">With gratitude</h3>
                <p class="mx-auto mt-4 max-w-md text-[#6a5c3e]">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-amber-700 to-amber-500 px-9 py-4 text-sm font-semibold text-white transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-amber-700/60">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
