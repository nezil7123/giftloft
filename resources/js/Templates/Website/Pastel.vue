<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { showRegistry, typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, venuePhoto, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);

const petals = Array.from({ length: 12 }, (_, i) => ({ left: (i * 71) % 100, delay: (i % 8) * 1.1, dur: 9 + (i % 5), size: 8 + (i % 4) * 3 }));
</script>

<template>
    <div ref="root" class="relative overflow-hidden bg-[#fbf7ff] text-[#3d3355]">
        <!-- floating pastel blobs -->
        <div class="pointer-events-none fixed inset-0 z-0">
            <div class="gl-blob absolute left-[8%] top-[12%] h-72 w-72 rounded-full bg-rose-200/50 blur-3xl"></div>
            <div class="gl-blob absolute right-[6%] top-[30%] h-80 w-80 rounded-full bg-sky-200/50 blur-3xl" style="animation-delay:-4s"></div>
            <div class="gl-blob absolute bottom-[10%] left-[30%] h-72 w-72 rounded-full bg-amber-200/40 blur-3xl" style="animation-delay:-8s"></div>
            <span v-for="(p,i) in petals" :key="i" class="gl-fall absolute top-[-5%] rounded-[50%_0_50%_0] bg-violet-300/50"
                :style="`left:${p.left}%;width:${p.size}px;height:${p.size}px;animation-delay:${p.delay}s;animation-duration:${p.dur}s`"></span>
        </div>

        <div class="relative z-10">
            <!-- HERO -->
            <section class="relative flex min-h-screen items-center justify-center px-6 py-24 text-center">
                <div data-fx="hero-exit">
                    <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-violet-500/80">{{ typeLabel }}</p>
                    <h1 class="mt-8 overflow-hidden">
                        <span class="gl-clip gl-display block font-serif font-medium leading-[0.9]">
                            <span class="bg-gradient-to-r from-rose-400 via-violet-400 to-sky-400 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                        </span>
                    </h1>
                    <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-lg font-light text-violet-600/80 sm:text-2xl">{{ tagline }}</p>
                    <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.35em] text-[#8a7ba5]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                    <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-3">
                        <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="flex h-20 w-20 flex-col items-center justify-center rounded-3xl bg-white/70 shadow-lg shadow-violet-200/50 backdrop-blur-sm sm:h-24 sm:w-24">
                            <span class="text-2xl font-bold tabular-nums text-violet-500 sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                            <span class="mt-1 text-[9px] uppercase tracking-[0.25em] text-[#8a7ba5]">{{ u.l }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- COVER -->
            <section v-if="coverPhoto" class="px-6 pb-6 sm:px-10">
                <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] shadow-2xl shadow-violet-200/50">
                    <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover" />
                </div>
            </section>

            <!-- STORY -->
            <section v-if="event.description" class="px-6 py-28 sm:px-10">
                <div class="mx-auto max-w-3xl text-center">
                    <svg data-fx="draw" viewBox="0 0 120 60" fill="none" class="mx-auto mb-8 h-9 w-24 text-violet-400/70" aria-hidden="true">
                        <path d="M60 58 C60 40 60 30 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        <path d="M60 20 C50 14 44 6 50 2 C56 -2 62 6 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        <path d="M60 20 C70 14 76 6 70 2 C64 -2 58 6 60 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        <path d="M60 30 C48 28 40 34 42 40 C44 46 54 44 60 36" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        <path d="M60 30 C72 28 80 34 78 40 C76 46 66 44 60 36" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-violet-500/80">Our story</p>
                    <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-[#4d4168] sm:text-3xl">{{ event.description }}</p>
                </div>
            </section>

            <!-- DETAILS -->
            <section class="px-6 pb-10 sm:px-10">
                <!-- Bento mosaic -->
                <div data-fx="rise3d" class="mx-auto grid max-w-5xl auto-rows-[130px] grid-cols-3 gap-5">
                    <div data-fx="tilt3d" class="col-span-2 flex flex-col justify-center rounded-[2rem] bg-white/70 p-8 shadow-lg shadow-violet-100/50 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-500/80">When</p>
                        <p class="mt-3 font-serif text-2xl text-[#3d3355]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                    </div>
                    <div data-fx="tilt3d" :class="['col-span-1 flex flex-col justify-center rounded-[2rem] bg-white/70 p-6 shadow-lg shadow-violet-100/50 backdrop-blur-sm', dressCode ? 'row-span-2' : '']">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-500/80">Where</p>
                        <p class="mt-3 font-serif text-xl text-[#3d3355]">{{ location || 'To be announced' }}</p>
                    </div>
                    <div v-if="dressCode" data-fx="tilt3d" class="col-span-2 flex flex-col justify-center rounded-[2rem] bg-white/70 p-8 shadow-lg shadow-violet-100/50 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-500/80">Dress code</p>
                        <p class="mt-3 font-serif text-2xl text-[#3d3355]">{{ dressCode }}</p>
                    </div>
                </div>

                <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] bg-white/70 p-8 shadow-lg shadow-violet-100/50 sm:p-12">
                    <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-violet-500/80">The plan</p>
                    <div class="mt-6">
                        <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t border-violet-100 py-5">
                            <span class="w-24 shrink-0 text-sm uppercase tracking-widest text-violet-500/70">{{ s.time }}</span>
                            <div class="flex-1">
                                <p class="font-serif text-2xl text-[#3d3355]">{{ s.title }}</p>
                                <p v-if="s.detail" class="mt-1 text-sm text-[#8a7ba5]">{{ s.detail }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] bg-white/70 p-8 shadow-lg shadow-violet-100/50 sm:p-12">
                    <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-violet-500/80">Good to know</p>
                    <div class="mt-6 space-y-6">
                        <div v-for="(f,i) in faqs" :key="i" data-reveal>
                            <p class="font-serif text-xl text-[#3d3355]">{{ f.question }}</p>
                            <p class="mt-1.5 leading-7 text-[#6a5d85]">{{ f.answer }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- VENUE -->
            <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
                <div class="mx-auto max-w-5xl">
                    <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-violet-500/80">The place</p>
                    <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-[#3d3355] sm:text-5xl">{{ event.venue || location }}</h2>
                    <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-[#6a5d85]">{{ venueNote }}</p>
                    <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2.5rem] shadow-2xl shadow-violet-200/50">
                        <img :src="venuePhoto" alt="" loading="lazy" decoding="async" class="aspect-[16/10] w-full object-cover" />
                    </div>
                    <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                        <div v-if="travel" class="rounded-[2rem] bg-white/70 p-7 shadow-lg shadow-violet-100/50">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-500/80">Getting there</p>
                            <p class="mt-3 text-sm leading-7 text-[#5a4d75]">{{ travel }}</p>
                        </div>
                        <div v-if="stay" class="rounded-[2rem] bg-white/70 p-7 shadow-lg shadow-violet-100/50">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-violet-500/80">Where to stay</p>
                            <p class="mt-3 text-sm leading-7 text-[#5a4d75]">{{ stay }}</p>
                        </div>
                    </div>
                    <div v-if="mapUrl" class="mt-8 text-center">
                        <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-violet-400/50 px-7 py-3 text-sm font-semibold text-violet-600 transition hover:bg-violet-500/10">Open in Maps ↗</a>
                    </div>
                </div>
            </section>

            <!-- GALLERY -->
            <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-violet-500/80">Moments</p>
                <div class="mx-auto mt-10 grid max-w-5xl grid-cols-3 gap-6 sm:grid-cols-4">
                    <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.1 + (i % 3) * 0.1).toFixed(2)"
                        :class="['gl-photo group aspect-square overflow-hidden rounded-full shadow-lg shadow-violet-200/40', i % 3 === 1 ? 'mt-6' : '']">
                        <img :src="p" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" />
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="px-6 py-28 sm:px-10">
                <div v-if="showRegistry" data-reveal="zoom" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-rose-300 via-violet-300 to-sky-300 px-8 py-20 text-center text-white">
                    <h3 class="font-serif text-4xl sm:text-5xl">Wishlist</h3>
                    <p class="mx-auto mt-4 max-w-md text-white/90">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                    <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-semibold text-violet-600 transition hover:scale-105">View the registry</Link>
                </div>
                <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-[#8a7ba5]">{{ event.user?.name }} · ComeYay</p>
            </section>
        </div>
    </div>
</template>
