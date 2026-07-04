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

const stars = Array.from({ length: 60 }, (_, i) => ({ top: (i * 41) % 100, left: (i * 67) % 100, delay: (i % 11) * 0.35, size: i % 5 === 0 ? 2 : 1 }));
</script>

<template>
    <div ref="root" class="bg-[#040711] text-neutral-100">
        <!-- HERO — northern lights -->
        <section class="gl-grain relative flex min-h-screen items-center justify-center overflow-hidden py-24">
            <!-- Aurora ribbons drift at different parallax speeds -->
            <div class="pointer-events-none absolute inset-0">
                <div data-fx="parallax" data-speed="0.5" class="absolute -left-1/4 top-[-10%] h-[70vh] w-[150%] rotate-[-12deg] bg-gradient-to-r from-teal-400/25 via-sky-500/20 to-transparent blur-[70px]"></div>
                <div data-fx="parallax" data-speed="0.3" class="absolute -right-1/4 top-[15%] h-[55vh] w-[140%] rotate-[8deg] bg-gradient-to-l from-violet-500/25 via-fuchsia-500/15 to-transparent blur-[80px]"></div>
                <div data-fx="parallax" data-speed="0.7" class="absolute left-[10%] top-[40%] h-[45vh] w-[120%] rotate-[-5deg] bg-gradient-to-r from-emerald-400/15 via-cyan-400/15 to-transparent blur-[90px]"></div>
            </div>
            <div class="pointer-events-none absolute inset-0">
                <span v-for="(s,i) in stars" :key="i" class="gl-twinkle absolute rounded-full bg-white"
                    :style="`top:${s.top}%;left:${s.left}%;height:${s.size}px;width:${s.size}px;animation-delay:${s.delay}s`"></span>
            </div>

            <div data-fx="hero-exit" class="relative z-10 px-6 text-center">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-teal-300/90">{{ typeLabel }}</p>
                <h1 data-fx="chars" class="gl-display mt-8 font-serif font-medium">
                    <span class="bg-gradient-to-r from-teal-200 via-sky-200 to-violet-300 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-lg font-light tracking-wide text-sky-100/85 sm:text-2xl">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-4 text-sm uppercase tracking-[0.3em] text-neutral-400">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-3">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/[0.05] backdrop-blur-xl sm:h-24 sm:w-24">
                        <span class="bg-gradient-to-b from-white to-sky-200 bg-clip-text text-2xl font-bold tabular-nums text-transparent sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[9px] uppercase tracking-[0.25em] text-teal-200/70">{{ u.l }}</span>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.4em] text-neutral-500">Scroll</div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-8 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-3xl">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="relative overflow-hidden px-6 py-28 sm:px-10">
            <div data-fx="parallax" data-speed="0.35" class="pointer-events-none absolute right-[-10%] top-0 h-96 w-96 rounded-full bg-violet-500/10 blur-[100px]"></div>
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-teal-300/90">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-neutral-200 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-teal-300/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-300/80">When</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-violet-300/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300/80">Where</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-teal-300/90">The plan</p>
                <div class="mt-8">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="group flex items-baseline gap-6 border-t border-white/10 py-6 transition hover:border-teal-300/40">
                        <span class="w-24 shrink-0 text-sm uppercase tracking-widest text-sky-300/80">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-white transition group-hover:text-teal-200">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-neutral-400">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300/80">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-white">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-teal-300/90">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-white">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-neutral-400">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="relative overflow-hidden px-6 py-24 sm:px-10">
            <div data-fx="parallax" data-speed="0.4" class="pointer-events-none absolute left-[-10%] top-1/4 h-96 w-96 rounded-full bg-teal-400/10 blur-[100px]"></div>
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-teal-300/90">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-white sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-sky-100/70">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-3xl">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl border border-white/10 bg-white/[0.04] p-7 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-300/80">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl border border-white/10 bg-white/[0.04] p-7 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-300/80">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-teal-300/40 px-7 py-3 text-sm font-semibold text-teal-200 transition hover:bg-teal-300/10">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY — parallax columns -->
        <section v-if="gallery.length" class="overflow-hidden px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-teal-300/90">Moments</p>
            <div class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.12 + (i % 3) * 0.12).toFixed(2)"
                    class="gl-photo group overflow-hidden rounded-2xl border border-white/10">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] px-8 py-20 text-center" style="--gl-bg:#070b18">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(45,212,191,0.16),transparent_60%)]"></div>
                <h3 class="relative font-serif text-4xl text-white sm:text-5xl">Wishlist</h3>
                <p class="relative mx-auto mt-4 max-w-md text-neutral-300">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-teal-300 to-sky-400 px-9 py-4 text-sm font-semibold text-neutral-950 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-neutral-500">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
