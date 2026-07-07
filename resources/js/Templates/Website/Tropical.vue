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
    <div ref="root" class="bg-[#0c1f14] text-emerald-50">
        <!-- HERO -->
        <section class="gl-grain relative flex min-h-screen items-center justify-center overflow-hidden py-24">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_10%,rgba(52,211,153,0.22),transparent_60%)]"></div>
            <div data-fx="parallax" data-speed="0.4" class="gl-float pointer-events-none absolute -left-16 top-10 text-[9rem] opacity-20">🌿</div>
            <div data-fx="parallax" data-speed="0.6" class="gl-float pointer-events-none absolute -right-12 bottom-16 text-[8rem] opacity-20" style="animation-delay:-3s">🌴</div>

            <div data-fx="hero-exit" class="relative z-10 px-6 text-center">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-lime-300/90">{{ typeLabel }}</p>
                <h1 data-fx="chars" class="gl-display mt-8 font-serif font-medium">
                    <span class="bg-gradient-to-r from-lime-200 via-emerald-200 to-teal-200 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-lg font-light tracking-wide text-emerald-100/85 sm:text-2xl">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-4 text-sm uppercase tracking-[0.3em] text-emerald-300/70">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-3">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl border border-lime-300/20 bg-emerald-500/10 backdrop-blur-xl sm:h-24 sm:w-24">
                        <span class="text-2xl font-bold tabular-nums text-lime-100 sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[9px] uppercase tracking-[0.25em] text-emerald-300/70">{{ u.l }}</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-[0.4em] text-emerald-400/50">Scroll</div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-8 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-3xl ring-1 ring-lime-300/20">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="relative overflow-hidden px-6 py-28 sm:px-10">
            <div data-fx="parallax" data-speed="0.35" class="pointer-events-none absolute right-[-10%] top-0 h-96 w-96 rounded-full bg-teal-500/10 blur-[100px]"></div>
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-lime-300/90">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-emerald-100 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="rounded-3xl border border-lime-300/15 bg-emerald-500/[0.06] p-8 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300/80">When</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="rounded-3xl border border-lime-300/15 bg-emerald-500/[0.06] p-8 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300/80">Where</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-lime-300/15 bg-emerald-500/[0.04] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-lime-300/90">The plan</p>
                <div class="mt-6">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t border-lime-300/15 py-5">
                        <span class="w-24 shrink-0 text-sm uppercase tracking-widest text-lime-300/80">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-white">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-emerald-300/70">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl border border-lime-300/15 bg-emerald-500/[0.04] p-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300/80">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-white">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-lime-300/15 bg-emerald-500/[0.04] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-lime-300/90">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-white">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-emerald-200/70">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-lime-300/90">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-white sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-emerald-100/70">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-3xl ring-1 ring-lime-300/20">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl border border-lime-300/15 bg-emerald-500/[0.06] p-7 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300/80">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-emerald-200">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl border border-lime-300/15 bg-emerald-500/[0.06] p-7 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300/80">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-emerald-200">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-lime-300/40 px-7 py-3 text-sm font-semibold text-lime-200 transition hover:bg-lime-300/10">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="overflow-hidden px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-lime-300/90">Moments</p>
            <div class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.12 + (i % 3) * 0.12).toFixed(2)" class="gl-photo group overflow-hidden rounded-2xl border border-lime-300/15">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] px-8 py-20 text-center" style="--gl-bg:#0f2a1b">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(132,204,22,0.18),transparent_60%)]"></div>
                <h3 class="relative font-serif text-4xl text-white sm:text-5xl">Wishlist</h3>
                <p class="relative mx-auto mt-4 max-w-md text-emerald-200">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-lime-300 to-emerald-400 px-9 py-4 text-sm font-semibold text-emerald-950 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-emerald-400/60">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
