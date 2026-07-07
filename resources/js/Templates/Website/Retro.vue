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
    <div ref="root" class="bg-[#f6ead3] text-[#4a2e17]">
        <!-- HERO -->
        <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-24 text-center">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,#f4b860,transparent_60%)] opacity-60"></div>
            <div data-fx="rotate" data-turns="0.3" class="pointer-events-none absolute left-1/2 top-1/2 h-[120vmin] w-[120vmin] -translate-x-1/2 -translate-y-1/2 bg-[repeating-conic-gradient(#e8804a_0deg_18deg,transparent_18deg_36deg)] opacity-[0.06]"></div>

            <div data-fx="hero-exit" class="relative z-10">
                <p class="gl-enter inline-block rounded-full bg-[#e8804a] px-4 py-1.5 text-xs font-black uppercase tracking-[0.3em] text-[#f6ead3]">{{ typeLabel }}</p>
                <h1 data-fx="chars" class="mt-8 font-serif text-[15vw] font-black uppercase leading-[0.82] text-[#c14a1f] sm:text-[11vw]">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-xl font-bold text-[#8a5a2a] sm:text-2xl">{{ tagline }}</p>
                <p class="gl-enter gl-d3 mt-6 text-sm font-bold uppercase tracking-[0.3em] text-[#a06a35]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-3">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl bg-[#c14a1f] text-[#f6ead3] sm:h-24 sm:w-24">
                        <span class="text-2xl font-black tabular-nums sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[9px] font-bold uppercase tracking-[0.25em] text-[#f4b860]">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE BAND -->
        <div class="gl-marquee overflow-hidden border-y-2 border-[#4a2e17] bg-[#e8804a] py-3">
            <div class="flex whitespace-nowrap text-lg font-black uppercase tracking-widest text-[#4a2e17]">
                <span v-for="n in 8" :key="n" class="mx-6">{{ tagline || typeLabel }} ✺</span>
            </div>
        </div>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 py-10 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-5xl overflow-hidden rounded-[2rem] border-2 border-[#4a2e17]">
                <img :src="coverPhoto" alt="" class="aspect-[16/8] w-full object-cover sepia-[0.2]" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-xs font-black uppercase tracking-[0.5em] text-[#c14a1f]">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-medium leading-relaxed text-[#5a3a1e] sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-8">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-[#c14a1f]">When</p>
                    <p class="mt-3 font-serif text-2xl font-bold text-[#4a2e17]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-8">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-[#c14a1f]">Where</p>
                    <p class="mt-3 font-serif text-2xl font-bold text-[#4a2e17]">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-8 sm:p-12">
                <p data-reveal class="text-xs font-black uppercase tracking-[0.5em] text-[#c14a1f]">The plan</p>
                <div class="mt-6">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t-2 border-[#4a2e17]/20 py-5">
                        <span class="w-24 shrink-0 text-sm font-bold uppercase tracking-widest text-[#c14a1f]">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl font-bold text-[#4a2e17]">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-[#8a5a2a]">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-8 text-center">
                <p class="text-xs font-black uppercase tracking-[0.3em] text-[#c14a1f]">Dress code</p>
                <p class="mt-3 font-serif text-2xl font-bold text-[#4a2e17]">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-8 sm:p-12">
                <p data-reveal class="text-xs font-black uppercase tracking-[0.5em] text-[#c14a1f]">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal>
                        <p class="font-serif text-xl font-bold text-[#4a2e17]">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-[#6a4426]">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl">
                <p data-reveal class="text-center text-xs font-black uppercase tracking-[0.5em] text-[#c14a1f]">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl font-black uppercase text-[#c14a1f] sm:text-6xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-medium italic leading-8 text-[#6a4426]">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden rounded-[2rem] border-2 border-[#4a2e17]">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover sepia-[0.2]" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-5 sm:grid-cols-2">
                    <div v-if="travel" class="rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-7">
                        <p class="text-xs font-black uppercase tracking-[0.3em] text-[#c14a1f]">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-[#5a3a1e]">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rounded-[2rem] border-2 border-[#4a2e17] bg-[#f4b860]/40 p-7">
                        <p class="text-xs font-black uppercase tracking-[0.3em] text-[#c14a1f]">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-[#5a3a1e]">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="inline-flex rounded-full border-2 border-[#4a2e17] bg-[#c14a1f] px-7 py-3 text-sm font-black uppercase tracking-widest text-[#f6ead3] transition hover:scale-105">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-black uppercase tracking-[0.5em] text-[#c14a1f]">Moments</p>
            <div class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.1 + (i % 3) * 0.1).toFixed(2)" class="gl-photo group overflow-hidden rounded-2xl border-2 border-[#4a2e17]">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full object-cover sepia-[0.15] transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] border-2 border-[#4a2e17] bg-[#c14a1f] px-8 py-20 text-center text-[#f6ead3]">
                <h3 class="font-serif text-4xl font-black uppercase sm:text-6xl">Wishlist</h3>
                <p class="mx-auto mt-4 max-w-md text-[#f4b860]">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="mt-9 inline-flex items-center justify-center rounded-full bg-[#f6ead3] px-9 py-4 text-sm font-black uppercase tracking-widest text-[#c14a1f] transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs font-bold uppercase tracking-[0.3em] text-[#a06a35]">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
