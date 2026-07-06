<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, venuePhoto, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);

const hero = ref(null);
const title = ref(null);
const photoWrap = ref(null);
const photoImg = ref(null);
const heroMeta = ref(null);
const overlay = ref(null);

let ctx;
onMounted(() => {
    ctx = gsap.context(() => {
        const mm = gsap.matchMedia();

        mm.add('(prefers-reduced-motion: no-preference)', () => {
            // ── Act I (on load): the title arrives from outside the viewport ──
            const chars = title.value.querySelectorAll('.nova-char');
            gsap.from(chars, {
                y: '105vh',
                rotate: () => gsap.utils.random(-14, 14),
                duration: 1.4,
                ease: 'power4.out',
                stagger: { each: 0.045, from: 'random' },
            });
            gsap.from(heroMeta.value, { opacity: 0, y: 24, duration: 0.9, delay: 1.1, ease: 'power3.out' });

            // ── Act II (on scroll, pinned): the photo grows from a dot at the
            //    title's centre into a full-screen frame while the title flies
            //    past the camera ──
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: hero.value,
                    start: 'top top',
                    end: '+=260%',
                    scrub: true,
                    pin: true,
                    anticipatePin: 1,
                },
            });

            tl.fromTo(photoWrap.value,
                { clipPath: 'circle(0.55% at 50% 50%)' },
                { clipPath: 'circle(72% at 50% 50%)', ease: 'none', duration: 1 }, 0)
              .fromTo(photoImg.value, { scale: 1.7 }, { scale: 1, ease: 'none', duration: 1 }, 0)
              .to(title.value, { scale: 3.2, opacity: 0, ease: 'power1.in', duration: 0.55 }, 0)
              .to(heroMeta.value, { opacity: 0, duration: 0.15 }, 0)
              .fromTo(overlay.value, { opacity: 0, y: 48 }, { opacity: 1, y: 0, duration: 0.28 }, 0.66);
        });
    }, root.value);
});
onUnmounted(() => ctx?.revert());
</script>

<template>
    <div ref="root" class="bg-black text-neutral-100">
        <!-- ═══ HERO — launch sequence ═══ -->
        <section ref="hero" class="gl-gridlines relative flex h-screen items-center justify-center overflow-hidden bg-black">
            <!-- The photo — a dot that becomes the sky -->
            <div ref="photoWrap" class="absolute inset-0" style="clip-path: circle(72% at 50% 50%)">
                <img v-if="coverPhoto" ref="photoImg" :src="coverPhoto" alt="" class="h-full w-full object-cover" />
                <div v-else ref="photoImg" class="h-full w-full bg-[radial-gradient(circle_at_50%_40%,#312e81,#0c0a1d_70%)]"></div>
                <div class="absolute inset-0 bg-black/35"></div>
            </div>

            <!-- The title -->
            <div ref="title" class="relative z-10 px-6 text-center will-change-transform">
                <p class="mb-6 text-[10px] font-bold uppercase tracking-[0.7em] text-cyan-300/90">{{ typeLabel }} · {{ fmtDate(event.starts_at) }}</p>
                <h1 class="gl-display font-black uppercase tracking-tight text-white">
                    <span v-for="(c, i) in (hosts || event.title)" :key="i" class="nova-char inline-block" :style="c === ' ' ? 'white-space:pre' : ''">{{ c }}</span>
                </h1>
                <p v-if="tagline" class="mt-6 text-sm font-medium uppercase tracking-[0.35em] text-neutral-300">{{ tagline }}</p>
            </div>

            <!-- Scroll cue — centered with inset-x, not translate (GSAP owns this element's transform) -->
            <div ref="heroMeta" class="absolute inset-x-0 bottom-8 z-10 text-center">
                <p class="text-[10px] font-bold uppercase tracking-[0.5em] text-neutral-400">Scroll to begin</p>
                <div class="mx-auto mt-3 h-8 w-px animate-pulse bg-gradient-to-b from-cyan-300 to-transparent"></div>
            </div>

            <!-- Caption that lands once the photo is full screen -->
            <div ref="overlay" class="absolute inset-x-0 bottom-14 z-10 px-6 text-center opacity-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.55em] text-cyan-300">{{ location || 'Somewhere unforgettable' }}</p>
                <p class="mt-3 font-serif text-3xl text-white sm:text-4xl">{{ tagline || 'The countdown has started' }}</p>
            </div>
        </section>

        <!-- ═══ T-MINUS — countdown HUD ═══ -->
        <section v-if="countdown" class="relative px-6 py-24 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow relative mx-auto max-w-3xl rounded-3xl px-6 py-12 text-center" style="--gl-bg:#05050c">
                <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300">T-minus</p>
                <!-- items-start + identical leading-none sizes keep the colons on the digit row -->
                <div class="mt-8 flex items-start justify-center gap-2 sm:gap-4">
                    <template v-for="(u, i) in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hours'},{v:countdown.mins,l:'Minutes'},{v:countdown.secs,l:'Seconds'}]" :key="u.l">
                        <span v-if="i" class="text-4xl font-black leading-none text-cyan-400/50 sm:text-6xl">:</span>
                        <div class="flex flex-col items-center">
                            <span class="bg-gradient-to-b from-white to-cyan-200 bg-clip-text text-4xl font-black leading-none tabular-nums text-transparent sm:text-6xl">{{ String(u.v).padStart(2,'0') }}</span>
                            <span class="mt-3 text-[9px] font-bold uppercase tracking-[0.3em] text-neutral-500">{{ u.l }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- ═══ TRANSMISSION — the story ═══ -->
        <section v-if="event.description" class="relative overflow-hidden px-6 py-28 sm:px-10">
            <div data-fx="parallax" data-speed="0.35" class="pointer-events-none absolute -right-32 top-0 h-96 w-96 rounded-full bg-cyan-500/10 blur-[110px]"></div>
            <div class="mx-auto max-w-3xl">
                <p data-reveal class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300"><span class="h-px w-10 bg-cyan-400/60"></span><span data-fx="decrypt">Incoming transmission</span></p>
                <p data-fx="words" class="mt-8 whitespace-pre-line text-2xl font-light leading-relaxed text-neutral-200 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- ═══ MISSION DATA — details in HUD cards ═══ -->
        <section class="px-6 pb-12 sm:px-10">
            <div data-fx="rise3d" class="mx-auto grid max-w-5xl gap-4 sm:grid-cols-2">
                <div data-fx="tilt3d" class="nova-hud relative p-8">
                    <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300/80">Launch window</p>
                    <p class="mt-3 text-2xl font-bold text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div data-fx="tilt3d" class="nova-hud relative p-8">
                    <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300/80">Coordinates</p>
                    <p class="mt-3 text-2xl font-bold text-white">{{ location || 'To be announced' }}</p>
                </div>
                <div v-if="dressCode" data-fx="tilt3d" class="nova-hud relative p-8 sm:col-span-2">
                    <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300/80">Uniform</p>
                    <p class="mt-3 text-2xl font-bold text-white">{{ dressCode }}</p>
                </div>
            </div>

            <!-- Flight plan -->
            <div v-if="schedule.length" class="mx-auto mt-4 max-w-5xl">
                <div class="nova-hud relative p-8 sm:p-12">
                    <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300">Flight plan</p>
                    <div class="mt-8">
                        <div v-for="(s,i) in schedule" :key="i" data-reveal class="group flex items-baseline gap-6 border-t border-white/10 py-6 transition hover:border-cyan-400/40">
                            <span class="font-mono text-sm text-cyan-300/70">{{ s.time }}</span>
                            <div class="flex-1">
                                <p class="text-xl font-bold text-white transition group-hover:text-cyan-200">{{ s.title }}</p>
                                <p v-if="s.detail" class="mt-1 text-sm text-neutral-400">{{ s.detail }}</p>
                            </div>
                            <span class="font-mono text-xs text-neutral-600">{{ String(i+1).padStart(2,'0') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="faqs.length" data-fx="rise3d" class="mx-auto mt-4 grid max-w-5xl gap-4 sm:grid-cols-2">
                <div v-for="(f,i) in faqs" :key="i" data-fx="tilt3d" class="nova-hud relative p-7">
                    <p class="text-lg font-bold text-white">{{ f.question }}</p>
                    <p class="mt-2 leading-7 text-neutral-400">{{ f.answer }}</p>
                </div>
            </div>
        </section>

        <!-- ═══ DESTINATION — venue ═══ -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="relative overflow-hidden px-6 py-24 sm:px-10">
            <div data-fx="parallax" data-speed="0.4" class="pointer-events-none absolute -left-32 top-1/3 h-96 w-96 rounded-full bg-violet-500/10 blur-[110px]"></div>
            <div class="mx-auto max-w-5xl">
                <p data-fx="decrypt" class="text-center text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300">Destination</p>
                <h2 data-fx="chars" class="mt-5 text-center text-4xl font-black uppercase tracking-tight text-white sm:text-6xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center text-lg font-light leading-8 text-neutral-300">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-12 max-w-2xl overflow-hidden rounded-3xl">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/9] w-full object-cover" />
                </div>
                <div data-fx="rise3d" class="mt-10 grid gap-4 sm:grid-cols-2">
                    <div v-if="travel" data-fx="tilt3d" class="nova-hud relative p-7">
                        <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300/80">Approach vector</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ travel }}</p>
                    </div>
                    <div v-if="stay" data-fx="tilt3d" class="nova-hud relative p-7">
                        <p data-fx="decrypt" class="text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300/80">Docking &amp; lodging</p>
                        <p class="mt-3 text-sm leading-7 text-neutral-300">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-cyan-400/50 px-7 py-3 text-xs font-bold uppercase tracking-[0.25em] text-cyan-200 transition hover:bg-cyan-400/10">Lock coordinates ↗</a>
                </div>
            </div>
        </section>

        <!-- ═══ ARCHIVE — gallery flies in from depth ═══ -->
        <section v-if="gallery.length" class="px-6 py-20 sm:px-10">
            <p data-fx="decrypt" class="text-center text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300">From the archive</p>
            <!-- aspect-square lives on the tile (not the img) so spanning tiles fill their grid area exactly -->
            <div data-fx="rise3d" class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="tilt3d"
                    class="gl-photo group relative overflow-hidden rounded-2xl border border-white/10 transition-colors duration-500 hover:border-cyan-400/50 hover:shadow-[0_0_44px_-10px_rgba(34,211,238,0.45)]"
                    :class="i % 5 === 0 ? 'col-span-2 row-span-2 aspect-square' : 'aspect-square'">
                    <img :src="p" alt="" loading="lazy" class="h-full w-full object-cover" />
                    <span class="absolute left-3 top-3 font-mono text-[9px] uppercase tracking-widest text-cyan-200/0 transition group-hover:text-cyan-200/90">IMG_{{ String(i+1).padStart(3,'0') }}</span>
                </div>
            </div>
        </section>

        <!-- ═══ CTA ═══ -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow gl-grain relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] px-8 py-20 text-center" style="--gl-bg:#05050c">
                <div class="gl-pan pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(34,211,238,0.14),transparent_60%)]"></div>
                <p data-fx="decrypt" class="relative text-[10px] font-bold uppercase tracking-[0.6em] text-cyan-300">Final directive</p>
                <h3 data-fx="chars" class="relative mt-5 text-4xl font-black uppercase tracking-tight text-white sm:text-5xl">Send a gift</h3>
                <p class="relative mx-auto mt-4 max-w-md text-neutral-400">{{ rsvpNote || 'Browse our wishlist and send something unforgettable.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-cyan-300 px-9 py-4 text-sm font-black uppercase tracking-[0.2em] text-black transition hover:scale-105">Open the registry</Link>
            </div>
            <p class="mt-10 text-center text-[10px] uppercase tracking-[0.4em] text-neutral-600">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>

<style scoped>
/* HUD card — glass panel with corner brackets */
.nova-hud {
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.03);
}
.nova-hud::before,
.nova-hud::after {
    content: '';
    position: absolute;
    width: 14px;
    height: 14px;
    pointer-events: none;
    transition: border-color 0.4s ease;
}
.nova-hud::before {
    top: -1px;
    left: -1px;
    border-top: 2px solid rgba(34, 211, 238, 0.65);
    border-left: 2px solid rgba(34, 211, 238, 0.65);
    border-top-left-radius: 1rem;
}
.nova-hud::after {
    bottom: -1px;
    right: -1px;
    border-bottom: 2px solid rgba(34, 211, 238, 0.65);
    border-right: 2px solid rgba(34, 211, 238, 0.65);
    border-bottom-right-radius: 1rem;
}
.nova-hud:hover::before,
.nova-hud:hover::after {
    border-color: rgba(34, 211, 238, 1);
}
</style>
