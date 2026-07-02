<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);

const leaves = ['🌿', '🍃', '🌸', '🌷', '🌼', '🍃', '🌸'];
const marquee = computed(() => [hosts.value || props.event.title, '❧', fmtDate(props.event.starts_at), '❧', location.value || 'Celebrate with us', '❧']);
</script>

<template>
    <div ref="root" class="bg-[#f3f4ee] text-emerald-950">
        <!-- HERO — arch photo, drifting garden -->
        <section class="gl-grain relative overflow-hidden pb-20 pt-28">
            <div class="gl-mesh-warm gl-mesh-anim absolute inset-0 opacity-60"></div>
            <div class="gl-aurora opacity-20"></div>
            <!-- drifting leaves -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <span v-for="(l,i) in leaves" :key="i" class="gl-fall absolute top-0 text-3xl opacity-50"
                    :style="`left:${i*14+4}%;animation-delay:${i*1.3}s;animation-duration:${10+i}s`">{{ l }}</span>
            </div>
            <div class="gl-spin-slow pointer-events-none absolute -right-40 -top-40 h-96 w-96 rounded-full border border-emerald-700/10"></div>

            <div data-fx="hero-exit" class="relative z-10 mx-auto max-w-6xl px-6 text-center sm:px-10">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.5em] text-emerald-700">{{ typeLabel }}</p>
                <div class="gl-enter gl-d1 mx-auto mt-5 flex items-center justify-center gap-3 text-emerald-600/60">
                    <span class="gl-grow h-px w-14 bg-emerald-600/40"></span>❀<span class="gl-grow h-px w-14 bg-emerald-600/40"></span>
                </div>
                <h1 class="mt-6 overflow-hidden">
                    <span class="gl-clip gl-display block font-serif font-medium text-emerald-950">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-2xl italic text-emerald-700">{{ tagline }}</p>

                <!-- Arch photo -->
                <div v-if="coverPhoto" class="gl-enter gl-d2 relative mx-auto mt-14 w-full max-w-lg">
                    <div class="gl-photo overflow-hidden rounded-t-[12rem] rounded-b-[2rem] border-8 border-white shadow-2xl shadow-emerald-900/20">
                        <img :src="coverPhoto" alt="" class="gl-ken aspect-[3/4] w-full object-cover" />
                    </div>
                    <div class="gl-float pointer-events-none absolute -left-10 top-10 text-6xl opacity-60">🌿</div>
                    <div class="gl-float pointer-events-none absolute -right-8 bottom-14 text-5xl opacity-60" style="animation-delay:2.5s">🍃</div>
                    <p class="mt-5 font-serif text-lg italic text-emerald-700/80">{{ fmtDate(event.starts_at) }}</p>
                </div>

                <div v-if="countdown" class="gl-enter gl-d3 mt-12 inline-flex gap-8">
                    <div v-for="(u,idx) in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l" class="gl-float-soft text-center" :style="`animation-delay:${idx*0.4}s`">
                        <p class="font-serif text-5xl font-medium tabular-nums text-emerald-800">{{ String(u.v).padStart(2,'0') }}</p>
                        <p class="mt-1 text-[10px] uppercase tracking-widest text-emerald-600/60">{{ u.l }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden border-y border-emerald-800/15 bg-emerald-800 py-4 text-[#f3f4ee]">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 font-serif text-2xl" :class="m === '❧' ? 'text-emerald-300' : ''">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="font-serif text-sm uppercase tracking-[0.4em] text-emerald-600">Our story</p>
                <p data-reveal class="mt-7 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-emerald-950/85 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div data-reveal="left" class="gl-tilt rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 backdrop-blur-sm">
                    <p class="font-serif text-xs uppercase tracking-[0.3em] text-emerald-600">When</p>
                    <p class="mt-3 font-serif text-2xl text-emerald-950">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div data-reveal="right" class="gl-tilt rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 backdrop-blur-sm">
                    <p class="font-serif text-xs uppercase tracking-[0.3em] text-emerald-600">Where</p>
                    <p class="mt-3 font-serif text-2xl text-emerald-950">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 backdrop-blur-sm sm:p-12">
                <p data-reveal class="text-center font-serif text-sm uppercase tracking-[0.4em] text-emerald-600">Schedule</p>
                <div class="mt-8 space-y-0">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t border-emerald-900/10 py-6">
                        <span class="font-serif text-3xl font-light text-emerald-600/40">{{ String(i+1).padStart(2,'0') }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-emerald-950">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-emerald-700/60">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm uppercase tracking-widest text-emerald-700/70">{{ s.time }}</span>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 text-center backdrop-blur-sm">
                <p class="font-serif text-xs uppercase tracking-[0.3em] text-emerald-600">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-emerald-950">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 backdrop-blur-sm sm:p-12">
                <p data-reveal class="font-serif text-sm uppercase tracking-[0.4em] text-emerald-600">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-emerald-950">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-emerald-800/65">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE — the garden gate -->
        <section v-if="event.venue || venueNote || travel || stay" class="relative px-6 py-24 sm:px-10">
            <span data-fx="parallax" data-speed="0.35" class="pointer-events-none absolute left-4 top-10 text-7xl opacity-15">🌿</span>
            <span data-fx="parallax" data-speed="0.5" class="pointer-events-none absolute bottom-10 right-6 text-7xl opacity-15">🍃</span>
            <div class="mx-auto max-w-4xl">
                <svg data-fx="draw" viewBox="0 0 400 60" fill="none" class="mx-auto mb-10 h-12 w-full max-w-md text-emerald-600/60">
                    <path d="M10 30 C 60 5, 90 55, 140 30 S 220 5, 270 30 S 350 55, 390 30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M140 30 q -6 -14 -18 -16 M140 30 q 6 -14 18 -16 M270 30 q -6 14 -18 16 M270 30 q 6 14 18 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <p data-reveal class="text-center font-serif text-sm uppercase tracking-[0.4em] text-emerald-600">The garden gate</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-emerald-950 sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-reveal class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl italic leading-8 text-emerald-800/75">{{ venueNote }}</p>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-7 backdrop-blur-sm">
                        <p class="font-serif text-xs uppercase tracking-[0.3em] text-emerald-600">Getting there</p>
                        <p class="mt-3 leading-7 text-emerald-900/70">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-7 backdrop-blur-sm">
                        <p class="font-serif text-xs uppercase tracking-[0.3em] text-emerald-600">Where to stay</p>
                        <p class="mt-3 leading-7 text-emerald-900/70">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex items-center gap-2 rounded-full bg-emerald-800 px-7 py-3 text-sm font-semibold text-white transition hover:scale-105">Open in Maps 🌿</a>
                </div>
            </div>
        </section>

        <!-- GALLERY — organic arches & circles -->
        <section v-if="gallery.length" class="px-6 pb-10 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center font-serif text-sm uppercase tracking-[0.4em] text-emerald-600">Memories</p>
                <div class="mt-10 flex flex-wrap items-end justify-center gap-6">
                    <div v-for="(p,i) in gallery" :key="i" data-reveal
                        class="gl-photo gl-tilt overflow-hidden border-8 border-white shadow-xl shadow-emerald-900/15"
                        :class="[
                            i % 3 === 0 ? 'w-56 rounded-t-[7rem] rounded-b-3xl sm:w-64' : i % 3 === 1 ? 'w-44 rounded-full sm:w-52' : 'w-52 rounded-[2.5rem] sm:w-60',
                        ]">
                        <img :src="p" alt="" loading="lazy" class="w-full object-cover" :class="i % 3 === 1 ? 'aspect-square' : 'aspect-[3/4]'" />
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-grain relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] bg-emerald-800 px-8 py-20 text-center text-white">
                <div class="gl-aurora opacity-25"></div>
                <div class="gl-float pointer-events-none absolute -left-6 -top-6 text-8xl opacity-20">🌿</div>
                <div class="gl-float pointer-events-none absolute -bottom-8 -right-6 text-8xl opacity-20" style="animation-delay:3s">🍃</div>
                <h3 class="relative font-serif text-4xl sm:text-5xl">Gift Registry</h3>
                <p class="relative mx-auto mt-4 max-w-md text-emerald-100/80">{{ rsvpNote || 'Browse our wishlist and send a little something.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-semibold text-emerald-800 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center font-serif text-sm italic text-emerald-700/60">With love, {{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
