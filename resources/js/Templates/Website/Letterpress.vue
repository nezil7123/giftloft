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
    <div ref="root" class="gl-grain bg-[#f4f1ea] text-[#2b2a27]">
        <!-- HERO -->
        <section class="relative flex min-h-screen items-center justify-center px-6 py-24 text-center">
            <div class="pointer-events-none absolute inset-6 border border-[#2b2a27]/20 sm:inset-10"></div>
            <div data-fx="hero-exit" class="relative z-10 max-w-3xl">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.6em] text-[#8a857a]">{{ typeLabel }}</p>
                <div class="gl-enter gl-d1 mx-auto mt-8 h-px w-20 bg-[#2b2a27]/30"></div>
                <h1 data-fx="chars" class="gl-display mt-8 font-serif font-normal leading-[0.92] text-[#2b2a27]">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-6 font-serif text-xl italic text-[#5a564d] sm:text-2xl">{{ tagline }}</p>
                <div class="gl-enter gl-d3 mx-auto mt-8 h-px w-20 bg-[#2b2a27]/30"></div>
                <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.35em] text-[#8a857a]">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-12 flex items-center justify-center gap-6 sm:gap-8">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l" class="text-center">
                        <div class="font-serif text-4xl font-light tabular-nums text-[#2b2a27] sm:text-5xl">{{ String(u.v).padStart(2,'0') }}</div>
                        <div class="mt-1 text-[10px] uppercase tracking-[0.3em] text-[#9a958a]">{{ u.l }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-6 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-4xl overflow-hidden">
                <img :src="coverPhoto" alt="" class="aspect-[16/9] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-2xl text-center">
                <p data-reveal class="text-xs font-semibold uppercase tracking-[0.5em] text-[#8a857a]">Our story</p>
                <p data-fx="words" class="mt-8 whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-[#3a382f] sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-4xl gap-px overflow-hidden border border-[#2b2a27]/15 bg-[#2b2a27]/10 sm:grid-cols-2">
                <div class="bg-[#f4f1ea] p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a857a]">When</p>
                    <p class="mt-3 font-serif text-2xl text-[#2b2a27]">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="bg-[#f4f1ea] p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a857a]">Where</p>
                    <p class="mt-3 font-serif text-2xl text-[#2b2a27]">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-10 max-w-4xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-[#8a857a]">The day</p>
                <div class="mt-6">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="flex items-baseline gap-6 border-t border-[#2b2a27]/15 py-5">
                        <span class="w-24 shrink-0 text-sm uppercase tracking-widest text-[#8a857a]">{{ s.time }}</span>
                        <div class="flex-1">
                            <p class="font-serif text-2xl text-[#2b2a27]">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-[#7a756a]">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-10 max-w-4xl border-t border-[#2b2a27]/15 pt-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a857a]">Dress code</p>
                <p class="mt-3 font-serif text-2xl text-[#2b2a27]">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-10 max-w-4xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-[#8a857a]">Good to know</p>
                <div class="mt-6 space-y-6">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal class="border-t border-[#2b2a27]/15 pt-4">
                        <p class="font-serif text-xl text-[#2b2a27]">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-[#5a564d]">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-4xl">
                <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-[#8a857a]">The place</p>
                <h2 data-fx="chars" class="mt-4 text-center font-serif text-4xl font-normal text-[#2b2a27] sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl text-center font-serif text-xl font-light italic leading-8 text-[#5a564d]">{{ venueNote }}</p>
                <div v-if="venuePhoto" data-fx="img-reveal" class="mx-auto mt-10 max-w-xl overflow-hidden">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-10 grid gap-8 sm:grid-cols-2">
                    <div v-if="travel" class="border-t border-[#2b2a27]/15 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a857a]">Getting there</p>
                        <p class="mt-3 text-sm leading-7 text-[#4a463d]">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="border-t border-[#2b2a27]/15 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a857a]">Where to stay</p>
                        <p class="mt-3 text-sm leading-7 text-[#4a463d]">{{ stay }}</p>
                    </div>
                </div>
                <div v-if="mapUrl" class="mt-8 text-center">
                    <a :href="mapUrl" target="_blank" rel="noopener" class="inline-flex border border-[#2b2a27]/40 px-7 py-3 text-sm font-semibold uppercase tracking-widest text-[#2b2a27] transition hover:bg-[#2b2a27] hover:text-[#f4f1ea]">Open in Maps ↗</a>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-xs font-semibold uppercase tracking-[0.5em] text-[#8a857a]">Moments</p>
            <div class="mx-auto mt-10 grid max-w-4xl grid-cols-2 gap-3 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="parallax" :data-speed="(0.1 + (i % 3) * 0.1).toFixed(2)" class="gl-photo group overflow-hidden">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full object-cover transition duration-700 group-hover:scale-105" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="mx-auto max-w-4xl border border-[#2b2a27]/20 px-8 py-20 text-center">
                <h3 class="font-serif text-4xl font-normal text-[#2b2a27] sm:text-5xl">With gratitude</h3>
                <p class="mx-auto mt-4 max-w-md text-[#5a564d]">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="mt-9 inline-flex items-center justify-center bg-[#2b2a27] px-9 py-4 text-sm font-semibold uppercase tracking-widest text-[#f4f1ea] transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs uppercase tracking-[0.3em] text-[#9a958a]">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
