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

const stars = Array.from({ length: 48 }, (_, i) => ({ top: (i * 37) % 100, left: (i * 53) % 100, delay: (i % 9) * 0.3, size: i % 4 === 0 ? 2 : 1 }));
const marquee = computed(() => [hosts.value || props.event.title, '✦', fmtDate(props.event.starts_at), '✦', location.value || 'An evening to remember', '✦']);
</script>

<template>
    <div ref="root" class="bg-[#080611] text-neutral-100">
        <!-- HERO — eclipse -->
        <section class="gl-grain gl-gridlines relative flex min-h-screen items-center justify-center overflow-hidden py-24">
            <div class="gl-pan pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(251,191,36,0.14),transparent_60%)]"></div>
            <div class="pointer-events-none absolute inset-0">
                <span v-for="(s,i) in stars" :key="i" class="gl-twinkle absolute rounded-full bg-amber-100"
                    :style="`top:${s.top}%;left:${s.left}%;height:${s.size}px;width:${s.size}px;animation-delay:${s.delay}s`"></span>
            </div>

            <div data-fx="hero-exit" class="relative z-10 px-6 text-center">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.55em] text-amber-300/80">{{ typeLabel }}</p>

                <!-- Eclipse photo -->
                <div class="relative mx-auto mt-10 h-64 w-64 sm:h-80 sm:w-80">
                    <div class="gl-spin-slow absolute -inset-5 rounded-full border border-dashed border-amber-300/30"></div>
                    <div data-fx="rotate" data-turns="-1" class="absolute -inset-10 rounded-full border border-dashed border-amber-300/10"></div>
                    <div class="gl-glow absolute inset-0 rounded-full" style="--gl-glow:rgba(251,191,36,0.35)"></div>
                    <div class="gl-photo gl-pop absolute inset-0 overflow-hidden rounded-full border-2 border-amber-300/40">
                        <img v-if="coverPhoto" :src="coverPhoto" alt="" class="gl-ken h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-500/20 to-transparent text-6xl">✦</div>
                    </div>
                    <span class="gl-twinkle absolute -right-2 top-6 text-2xl text-amber-200">✦</span>
                    <span class="gl-twinkle absolute -left-4 bottom-10 text-lg text-amber-200" style="animation-delay:1.2s">✦</span>
                </div>

                <h1 class="mt-10 overflow-hidden">
                    <span class="gl-clip gl-display block font-serif font-medium">
                        <span class="bg-gradient-to-b from-amber-100 to-amber-300 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                    </span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-2xl italic text-amber-200/90">{{ tagline }}</p>
                <div class="gl-grow mx-auto mt-7 h-px w-24 bg-gradient-to-r from-transparent via-amber-300/60 to-transparent"></div>

                <div v-if="countdown" class="gl-enter gl-d3 mt-10 inline-flex items-stretch gap-px overflow-hidden rounded-2xl border border-amber-300/20 bg-white/[0.04] backdrop-blur-md">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hours'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex w-20 flex-col items-center justify-center px-2 py-4 sm:w-24">
                        <span class="text-3xl font-bold tabular-nums text-amber-200 sm:text-4xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1.5 text-[9px] uppercase tracking-[0.25em] text-neutral-400">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden border-y border-amber-300/15 bg-[#080611] py-5">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 font-serif text-2xl tracking-tight" :class="m === '✦' ? 'text-amber-300/70' : 'text-amber-100/90'">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.4em] text-amber-300/80">Our story</p>
                <p data-reveal class="mt-7 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-neutral-200 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div data-reveal="left" class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.03] p-8 transition hover:border-amber-300/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/70">When</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div data-reveal="right" class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.03] p-8 transition hover:border-amber-300/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/70">Where</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.4em] text-amber-300/80">The evening</p>
                <div class="mt-8 space-y-0">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="group flex items-baseline gap-6 border-t border-white/10 py-6 transition hover:border-amber-300/30">
                        <span class="font-serif text-3xl font-light text-amber-300/40 transition group-hover:text-amber-300">{{ String(i+1).padStart(2,'0') }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-white">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-neutral-400">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm uppercase tracking-widest text-amber-200/70">{{ s.time }}</span>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/70">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-white">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.4em] text-amber-300/80">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-white">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-neutral-400">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE — under these stars -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <svg data-fx="draw" viewBox="0 0 400 80" fill="none" class="mx-auto mb-10 h-16 w-full max-w-lg text-amber-300/50">
                    <path d="M20 60 L90 25 L160 45 L230 15 L300 40 L380 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M90 25 l0.01 0 M230 15 l0.01 0 M300 40 l0.01 0" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
                </svg>
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.4em] text-amber-300/80">Under these stars</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-white sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-reveal class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-amber-100/70">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-reveal class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2rem] border border-amber-300/15">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-3xl border border-white/10 bg-white/[0.03] p-7 transition hover:border-amber-300/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/70">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-3xl border border-white/10 bg-white/[0.03] p-7 transition hover:border-amber-300/30">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/70">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-amber-300/40 px-7 py-3 text-sm font-semibold text-amber-200 transition hover:bg-amber-300/10">✦ Open in Maps</a>
                </div>
            </div>
        </section>

        <!-- GALLERY — golden constellations -->
        <section v-if="gallery.length" class="px-6 pb-10 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.4em] text-amber-300/80">Gallery</p>
                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                    <div v-for="(p,i) in gallery" :key="i" data-reveal
                        class="gl-photo group relative overflow-hidden rounded-2xl border border-amber-300/15 transition duration-500 hover:border-amber-300/50 hover:shadow-[0_0_40px_-8px_rgba(251,191,36,0.35)]"
                        :class="i % 5 === 0 ? 'col-span-2 row-span-2 aspect-square' : 'aspect-square'">
                        <img :src="p" alt="" loading="lazy" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#080611]/70 to-transparent opacity-60 transition group-hover:opacity-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow gl-grain relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] px-8 py-20 text-center" style="--gl-bg:#0c0917">
                <div class="gl-pan pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(251,191,36,0.15),transparent_65%)]"></div>
                <h3 class="relative font-serif text-4xl text-white sm:text-5xl">Wishlist</h3>
                <p class="relative mx-auto mt-4 max-w-md text-neutral-300">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-amber-300 px-9 py-4 text-sm font-semibold text-neutral-950 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-neutral-500">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
