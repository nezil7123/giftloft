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

const marquee = computed(() => [hosts.value || props.event.title, '✦', typeLabel.value, '✦', fmtDate(props.event.starts_at), '✦', location.value || 'Join us', '✦']);
</script>

<template>
    <div ref="root" class="bg-[#0d0b14] text-neutral-200">
        <!-- HERO — cinematic photo, ken burns, gold type -->
        <section class="gl-grain gl-gridlines relative flex min-h-screen items-center justify-center overflow-hidden">
            <img v-if="coverPhoto" :src="coverPhoto" alt="" class="gl-ken absolute inset-0 h-full w-full object-cover" />
            <div v-else class="gl-mesh-dark gl-mesh-anim absolute inset-0"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/40 to-[#0d0b14]"></div>
            <div class="gl-aurora opacity-25"></div>

            <!-- sparkles -->
            <div class="pointer-events-none absolute inset-0">
                <span v-for="i in 10" :key="i" class="gl-twinkle absolute h-1 w-1 rounded-full bg-amber-100"
                    :style="`top:${[14,28,18,64,30,72,48,8,58,84][i-1]}%;left:${[12,82,48,22,68,86,38,60,8,52][i-1]}%;animation-delay:${i*0.3}s`"></span>
            </div>

            <div data-fx="hero-exit" class="relative z-10 px-6 text-center">
                <p class="gl-enter mb-6 text-[11px] font-semibold uppercase tracking-[0.55em] text-amber-300/90">{{ typeLabel }}</p>
                <div class="gl-grow mx-auto mb-7 h-px w-28 bg-gradient-to-r from-transparent via-amber-300/70 to-transparent"></div>
                <h1 class="overflow-hidden">
                    <span class="gl-clip gl-display block font-serif font-medium text-white drop-shadow-[0_4px_30px_rgba(0,0,0,0.55)]">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-xl italic text-white/80 sm:text-2xl">{{ tagline }}</p>

                <div v-if="countdown" class="gl-enter gl-d3 gl-border-glow mt-12 inline-flex items-stretch gap-px overflow-hidden rounded-2xl backdrop-blur-md" style="--gl-bg:rgba(13,11,20,0.72)">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hours'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex w-20 flex-col items-center justify-center px-2 py-4 sm:w-24">
                        <span class="font-serif text-3xl font-medium tabular-nums text-white sm:text-4xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1.5 text-[9px] uppercase tracking-[0.25em] text-amber-200/60">{{ u.l }}</span>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/40">
                <svg class="gl-float-soft h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="gl-grain border-y border-amber-300/20 bg-[#0d0b14] py-5">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-5 font-serif text-2xl tracking-tight" :class="m === '✦' ? 'text-amber-300/70' : 'text-white/85'">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- STORY — photo split -->
        <section v-if="event.description" class="relative px-6 py-28 sm:px-10">
            <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-[0.9fr_1.1fr]">
                <div v-if="gallery.length" data-reveal="left" class="gl-photo gl-tilt relative overflow-hidden rounded-[2rem] border border-amber-300/15">
                    <img :src="gallery[0]" alt="" class="aspect-[4/5] w-full object-cover" loading="lazy" />
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#0d0b14]/60 to-transparent"></div>
                    <p class="absolute bottom-5 left-6 font-serif text-sm italic text-amber-100/80">{{ fmtDate(event.starts_at) }}</p>
                </div>
                <div data-reveal="right" class="text-center lg:text-left">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-amber-300/80">Our story</p>
                    <p class="mt-7 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-white/85 sm:text-3xl">{{ event.description }}</p>
                </div>
            </div>
        </section>

        <!-- DETAILS + SCHEDULE -->
        <section class="px-6 pb-10 sm:px-10">
            <div class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div data-reveal="left" class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.03] p-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">When</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div data-reveal="right" class="gl-tilt rounded-3xl border border-white/10 bg-white/[0.03] p-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">Where</p>
                    <p class="mt-3 font-serif text-2xl text-white">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-[11px] font-semibold uppercase tracking-[0.4em] text-amber-300/80">The day</p>
                <div class="mt-8 space-y-0">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="group flex items-baseline gap-6 border-t border-white/10 py-6 transition hover:border-amber-300/30">
                        <span class="font-serif text-3xl font-light text-amber-300/50 transition group-hover:text-amber-300">{{ String(i+1).padStart(2,'0') }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-white">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-white/50">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm uppercase tracking-widest text-white/60">{{ s.time }}</span>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 text-center">
                <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-white">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-12">
                <p data-reveal class="text-[11px] font-semibold uppercase tracking-[0.4em] text-amber-300/80">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-white">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-white/55">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE — the setting -->
        <section v-if="event.venue || venueNote || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-6xl">
                <p data-reveal class="text-center text-[11px] font-semibold uppercase tracking-[0.4em] text-amber-300/80">The setting</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-white sm:text-6xl">{{ event.venue || location }}</h2>
                <div class="mt-14 grid items-start gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div v-if="gallery[1] || coverPhoto" class="relative">
                        <div class="overflow-hidden rounded-[2rem] border border-amber-300/15">
                            <img :src="gallery[1] || coverPhoto" alt="" data-fx="parallax" data-speed="0.12" loading="lazy" class="aspect-[16/11] w-full scale-125 object-cover" />
                        </div>
                        <div data-fx="rotate" data-turns="0.6" class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full border border-dashed border-amber-300/40"></div>
                    </div>
                    <div class="space-y-5">
                        <div data-reveal class="rounded-3xl border border-white/10 bg-white/[0.03] p-7">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">About the venue</p>
                            <p class="mt-3 font-serif text-lg leading-8 text-white/80">{{ venueNote || location }}</p>
                            <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="gl-btn mt-5 inline-flex items-center gap-2 rounded-full border border-amber-300/40 px-6 py-2.5 text-sm font-semibold text-amber-200 transition hover:bg-amber-300/10">📍 Open in Maps</a>
                        </div>
                        <div data-fx="batch" class="grid gap-5 sm:grid-cols-2">
                            <div v-if="travel" class="rounded-3xl border border-white/10 bg-white/[0.03] p-6">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">🚗 Getting there</p>
                                <p class="mt-3 text-sm leading-7 text-white/65">{{ travel }}</p>
                            </div>
                            <div v-if="stay" class="rounded-3xl border border-white/10 bg-white/[0.03] p-6">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-amber-300/70">🏨 Where to stay</p>
                                <p class="mt-3 text-sm leading-7 text-white/65">{{ stay }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALLERY — masonry with gold edges -->
        <section v-if="gallery.length > 1" class="px-6 pb-10 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-[11px] font-semibold uppercase tracking-[0.4em] text-amber-300/80">Moments</p>
                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                    <div v-for="(p,i) in gallery.slice(1)" :key="i" data-reveal
                        class="gl-photo group relative overflow-hidden rounded-2xl border border-white/10 transition hover:border-amber-300/40"
                        :class="i % 5 === 0 ? 'col-span-2 row-span-2 aspect-square' : 'aspect-square'">
                        <img :src="p" alt="" loading="lazy" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- REGISTRY CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-border-glow gl-grain relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem]" style="--gl-bg:#12101c">
                <div class="gl-aurora opacity-30"></div>
                <div class="relative px-8 py-20 text-center">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-white/70">With love</p>
                    <h3 data-fx="chars" class="mt-4 font-serif text-4xl text-white sm:text-5xl">Gift Registry</h3>
                    <p class="mx-auto mt-4 max-w-md text-white/70">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                    <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-semibold text-neutral-900 transition hover:scale-105">View the registry</Link>
                </div>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-white/30">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
