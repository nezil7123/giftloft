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

const petals = Array.from({ length: 10 }, (_, i) => ({ left: (i * 61) % 100, delay: (i % 8) * 0.9, dur: 7 + (i % 5), size: 8 + (i % 4) * 3 }));
</script>

<template>
    <div ref="root" class="relative overflow-hidden bg-[#fff5f7] text-[#4a2733]">
        <!-- HERO: split — copy left, full-bleed photo right -->
        <section class="relative overflow-hidden lg:grid lg:min-h-screen lg:grid-cols-2">
            <div class="relative flex flex-col justify-center overflow-hidden px-6 py-24 text-center sm:px-10 lg:px-16 lg:py-16 lg:text-left">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(251,207,232,0.5),transparent_65%)]"></div>
                <div data-fx="hero-exit" class="relative z-10 mx-auto max-w-md lg:mx-0">
                    <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-rose-500/80">{{ typeLabel }}</p>
                    <h1 class="mt-6 overflow-hidden">
                        <span class="gl-clip gl-display block font-serif font-medium leading-[0.9]">
                            <span class="bg-gradient-to-r from-rose-500 via-pink-500 to-fuchsia-500 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                        </span>
                    </h1>
                    <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-xl italic text-rose-600/80 sm:text-2xl">{{ tagline }}</p>
                    <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.35em] text-[#a06776]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                    <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-4 sm:gap-6 lg:justify-start">
                        <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="text-center">
                            <div class="font-serif text-4xl font-medium tabular-nums text-rose-600 sm:text-5xl">{{ String(u.v).padStart(2,'0') }}</div>
                            <div class="mt-1 text-[10px] uppercase tracking-[0.3em] text-[#b98999]">{{ u.l }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative h-[55vh] overflow-hidden lg:h-auto">
                <img v-if="coverPhoto" data-fx="parallax" data-speed="0.15" :src="coverPhoto" alt="" class="h-[130%] w-full object-cover" />
                <div v-else class="flex h-full w-full items-center justify-center bg-[radial-gradient(circle_at_50%_40%,#ffd9e6,#ffb8ce)]">
                    <span class="gl-float text-[9rem] opacity-90">🌸</span>
                </div>
                <div class="pointer-events-none absolute inset-0">
                    <span v-for="(p,i) in petals" :key="i" class="gl-fall absolute top-[-5%] rounded-[50%_0_50%_0] bg-white/70"
                        :style="`left:${p.left}%;width:${p.size}px;height:${p.size}px;animation-delay:${p.delay}s;animation-duration:${p.dur}s`"></span>
                </div>
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#fff5f7] via-transparent to-transparent lg:bg-gradient-to-r lg:from-[#fff5f7]/50 lg:via-transparent lg:to-transparent"></div>
            </div>
        </section>

        <!-- STORY: pull-quote -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <span class="font-serif text-7xl leading-none text-rose-300/70" aria-hidden="true">&ldquo;</span>
                <p data-fx="words" class="-mt-4 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-[#5a2f3d] sm:text-3xl">{{ event.description }}</p>
                <p data-reveal class="mt-8 text-xs font-semibold uppercase tracking-[0.5em] text-rose-500/80">Our story</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="rise3d" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div data-fx="tilt3d" class="rounded-[2rem] bg-white/70 p-8 ring-1 ring-rose-200 backdrop-blur-sm transition-shadow hover:shadow-xl hover:shadow-rose-200/50">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500/80">When</p>
                    <p class="mt-3 font-serif text-2xl text-[#4a2733]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div data-fx="tilt3d" class="rounded-[2rem] bg-white/70 p-8 ring-1 ring-rose-200 backdrop-blur-sm transition-shadow hover:shadow-xl hover:shadow-rose-200/50">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500/80">Where</p>
                    <p class="mt-3 font-serif text-2xl text-[#4a2733]">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <!-- Schedule as a left-rail timeline -->
            <div v-if="schedule.length" class="mx-auto mt-16 max-w-2xl">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-rose-500/80">The day</p>
                <div class="relative mt-10 space-y-10 border-l border-rose-200 pl-8">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal="left" class="relative">
                        <span class="absolute -left-[2.35rem] top-1 h-3 w-3 rounded-full bg-rose-400 ring-4 ring-[#fff5f7]"></span>
                        <p class="text-xs uppercase tracking-[0.3em] text-rose-500/70">{{ s.time }}</p>
                        <p class="mt-1 font-serif text-2xl text-[#4a2733]">{{ s.title }}</p>
                        <p v-if="s.detail" class="mt-1 text-sm text-[#a06776]">{{ s.detail }}</p>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-16 max-w-5xl rounded-[2rem] bg-white/70 p-8 text-center ring-1 ring-rose-200">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500/80">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-[#4a2733]">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] bg-white/70 p-8 ring-1 ring-rose-200 sm:p-12">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-rose-500/80">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl text-[#4a2733]">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-[#7a4d5a]">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-rose-500/80">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl text-[#4a2733] sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-[#7a4d5a]">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2rem] ring-4 ring-white">
                    <img :src="venuePhoto" alt="" loading="lazy" decoding="async" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-[2rem] bg-white/70 p-7 ring-1 ring-rose-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500/80">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-[#6a3f4c]">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-[2rem] bg-white/70 p-7 ring-1 ring-rose-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-500/80">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-[#6a3f4c]">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="gl-btn inline-flex rounded-full border border-rose-400/50 px-7 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-500/10">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY: bento mosaic -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-rose-500/80">Moments</p>
            <div class="mx-auto mt-10 grid max-w-5xl auto-rows-[140px] grid-cols-3 gap-4 sm:auto-rows-[180px]">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.08 + (i % 3) * 0.08).toFixed(2)"
                    :class="['gl-photo group overflow-hidden rounded-2xl ring-2 ring-white', i % 5 === 0 ? 'col-span-2 row-span-2' : '']">
                    <img :src="p" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div v-if="showRegistry" data-reveal="zoom" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-rose-400 to-fuchsia-500 px-8 py-20 text-center text-white">
                <h3 class="font-serif text-4xl sm:text-5xl">Wishlist</h3>
                <p class="mx-auto mt-4 max-w-md text-rose-50/90">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-semibold text-rose-600 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-[#b98999]">{{ event.user?.name }} · ComeYay</p>
        </section>
    </div>
</template>
