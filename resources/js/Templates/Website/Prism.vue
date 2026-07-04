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

const marquee = computed(() => [hosts.value || props.event.title, '◆', tagline.value || typeLabel.value, '◆', fmtDate(props.event.starts_at), '◆']);
</script>

<template>
    <div ref="root" class="bg-[#fafaff] text-neutral-900">
        <!-- HERO — floating colour -->
        <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-24">
            <div class="pointer-events-none absolute inset-0">
                <div data-fx="parallax" data-speed="0.45" class="gl-blob absolute -left-24 top-10 h-96 w-96 rounded-full bg-gradient-to-br from-violet-400/50 to-fuchsia-300/40 blur-[80px]"></div>
                <div data-fx="parallax" data-speed="0.25" class="gl-blob absolute -right-20 top-1/3 h-[28rem] w-[28rem] rounded-full bg-gradient-to-br from-sky-300/50 to-teal-200/50 blur-[90px]" style="animation-delay:4s"></div>
                <div data-fx="parallax" data-speed="0.6" class="gl-blob absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-gradient-to-br from-amber-200/60 to-rose-300/50 blur-[70px]" style="animation-delay:8s"></div>
                <!-- Rotating geometry -->
                <div data-fx="rotate" data-turns="0.4" class="absolute left-[12%] top-[18%] hidden h-28 w-28 rounded-3xl border-2 border-violet-300/60 sm:block"></div>
                <div data-fx="rotate" data-turns="-0.6" class="absolute bottom-[20%] right-[10%] hidden h-20 w-20 rounded-full border-2 border-dashed border-sky-300/70 sm:block"></div>
            </div>

            <div data-fx="hero-exit" class="relative z-10 text-center">
                <span class="gl-enter inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white/70 px-5 py-2 text-xs font-bold uppercase tracking-[0.3em] text-neutral-600 shadow-sm backdrop-blur">{{ typeLabel }}</span>
                <h1 data-fx="chars" class="gl-display mt-8 font-black tracking-tight">
                    <span class="bg-gradient-to-r from-violet-600 via-fuchsia-500 to-sky-500 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="gl-enter gl-d2 mx-auto mt-6 max-w-lg text-lg font-medium text-neutral-600 sm:text-2xl">{{ tagline }}</p>

                <div v-if="countdown" class="gl-enter gl-d3 mt-12 inline-flex items-center gap-3 rounded-[2rem] border border-white/60 bg-white/60 p-3 shadow-xl shadow-violet-200/40 backdrop-blur-xl">
                    <div v-for="u in [{v:countdown.days,l:'Days',c:'from-violet-500 to-fuchsia-500'},{v:countdown.hours,l:'Hrs',c:'from-fuchsia-500 to-rose-400'},{v:countdown.mins,l:'Min',c:'from-sky-500 to-teal-400'},{v:countdown.secs,l:'Sec',c:'from-amber-400 to-orange-400'}]" :key="u.l"
                        class="flex h-20 w-20 flex-col items-center justify-center rounded-3xl bg-gradient-to-br text-white sm:h-24 sm:w-24" :class="u.c">
                        <span class="text-2xl font-black tabular-nums sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] opacity-80">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden border-y border-neutral-100 bg-white py-5">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 text-2xl font-black tracking-tight" :class="m === '◆' ? 'bg-gradient-to-r from-violet-500 to-sky-400 bg-clip-text text-transparent' : 'text-neutral-900'">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- STORY + COVER -->
        <section class="px-6 py-24 sm:px-10">
            <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
                <div v-if="coverPhoto" data-fx="img-reveal" class="overflow-hidden rounded-[2.5rem]">
                    <img :src="coverPhoto" alt="" class="aspect-[4/5] w-full object-cover" />
                </div>
                <div>
                    <p data-reveal class="text-xs font-black uppercase tracking-[0.4em] text-violet-500">Our story</p>
                    <p v-if="event.description" data-fx="words" class="mt-6 whitespace-pre-line text-2xl font-medium leading-relaxed text-neutral-800 sm:text-3xl">{{ event.description }}</p>
                    <p data-reveal class="mt-8 inline-flex items-center gap-2 rounded-full bg-neutral-900 px-5 py-2.5 text-sm font-bold text-white">{{ fmtDate(event.starts_at) }}<span v-if="location" class="text-neutral-400">· {{ location }}</span></p>
                </div>
            </div>
        </section>

        <!-- BENTO DETAILS -->
        <section class="px-6 pb-16 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-6xl gap-4 sm:grid-cols-3">
                <div class="gl-tilt rounded-[2rem] bg-gradient-to-br from-violet-500 to-fuchsia-500 p-8 text-white sm:col-span-2">
                    <p class="text-xs font-black uppercase tracking-[0.3em] opacity-70">When</p>
                    <p class="mt-3 text-3xl font-black">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="gl-tilt rounded-[2rem] bg-gradient-to-br from-sky-400 to-teal-400 p-8 text-white">
                    <p class="text-xs font-black uppercase tracking-[0.3em] opacity-70">Dress</p>
                    <p class="mt-3 text-2xl font-black">{{ dressCode || 'Come as you are' }}</p>
                </div>
                <div class="gl-tilt rounded-[2rem] bg-white p-8 shadow-lg shadow-neutral-200/60 ring-1 ring-neutral-100">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-neutral-400">Where</p>
                    <p class="mt-3 text-2xl font-black text-neutral-900">{{ location || 'To be announced' }}</p>
                </div>
                <div v-if="schedule.length" class="rounded-[2rem] bg-neutral-950 p-8 text-white sm:col-span-2">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-neutral-500">The plan</p>
                    <div class="mt-5 space-y-4">
                        <div v-for="(s,i) in schedule" :key="i" class="flex items-baseline gap-4 border-t border-white/10 pt-4">
                            <span class="w-20 shrink-0 text-sm font-bold" :class="['text-violet-400','text-sky-400','text-amber-400','text-rose-400'][i % 4]">{{ s.time }}</span>
                            <div>
                                <p class="text-lg font-bold">{{ s.title }}</p>
                                <p v-if="s.detail" class="text-sm text-neutral-400">{{ s.detail }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="faqs.length" data-fx="batch" class="mx-auto mt-4 grid max-w-6xl gap-4 sm:grid-cols-2">
                <div v-for="(f,i) in faqs" :key="i" class="rounded-[2rem] bg-white p-7 shadow-lg shadow-neutral-200/60 ring-1 ring-neutral-100">
                    <p class="text-lg font-black text-neutral-900">{{ f.question }}</p>
                    <p class="mt-2 leading-7 text-neutral-500">{{ f.answer }}</p>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="relative overflow-hidden bg-white px-6 py-24 sm:px-10">
            <div data-fx="parallax" data-speed="0.35" class="gl-blob pointer-events-none absolute -right-24 top-10 h-96 w-96 rounded-full bg-gradient-to-br from-teal-200/60 to-sky-200/50 blur-[80px]"></div>
            <div class="relative mx-auto max-w-6xl">
                <p data-reveal class="text-xs font-black uppercase tracking-[0.4em] text-sky-500">The venue</p>
                <h2 data-fx="chars" class="mt-4 text-4xl font-black tracking-tight text-neutral-900 sm:text-6xl">{{ event.venue || location }}</h2>
                <div class="mt-10 grid items-start gap-8 lg:grid-cols-2">
                    <div v-if="venuePhoto" data-fx="img-reveal" class="overflow-hidden rounded-[2.5rem]">
                        <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover" />
                    </div>
                    <div class="space-y-4">
                        <p v-if="venueNote" data-fx="words" class="text-xl font-medium leading-relaxed text-neutral-700">{{ venueNote }}</p>
                        <div v-if="travel" data-reveal class="rounded-[2rem] bg-gradient-to-br from-violet-50 to-fuchsia-50 p-7 ring-1 ring-violet-100">
                            <p class="text-xs font-black uppercase tracking-[0.3em] text-violet-500">Getting there</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-600">{{ travel }}</p>
                        </div>
                        <div v-if="stay" data-reveal class="rounded-[2rem] bg-gradient-to-br from-sky-50 to-teal-50 p-7 ring-1 ring-sky-100">
                            <p class="text-xs font-black uppercase tracking-[0.3em] text-sky-500">Where to stay</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-600">{{ stay }}</p>
                        </div>
                        <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" data-reveal class="gl-btn inline-flex rounded-full bg-neutral-900 px-7 py-3.5 text-sm font-bold text-white transition hover:scale-105">Open in Maps ↗</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section v-if="gallery.length" class="px-6 py-24 sm:px-10">
            <p data-reveal class="text-center text-xs font-black uppercase tracking-[0.4em] text-fuchsia-500">Moments</p>
            <div class="mx-auto mt-10 grid max-w-6xl grid-cols-2 gap-4 sm:grid-cols-3">
                <div v-for="(p,i) in gallery" :key="i" data-fx="scale-in"
                    class="gl-photo overflow-hidden shadow-lg shadow-neutral-200/50"
                    :class="i % 5 === 0
                        ? 'rounded-[3rem] aspect-square sm:col-span-2 sm:aspect-[2/1]'
                        : [i % 4 === 1 ? 'rounded-t-[3rem] rounded-b-xl' : i % 4 === 2 ? 'rounded-full' : i % 4 === 3 ? 'rounded-xl' : 'rounded-[3rem]', 'aspect-square']">
                    <img :src="p" alt="" loading="lazy" class="h-full w-full object-cover" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 pb-24 sm:px-10">
            <div data-reveal="zoom" class="relative mx-auto max-w-6xl overflow-hidden rounded-[3rem] bg-neutral-950 px-8 py-20 text-center text-white">
                <div class="gl-aurora"></div>
                <h3 class="relative text-4xl font-black tracking-tight sm:text-6xl">Make their day.<br /><span class="bg-gradient-to-r from-violet-400 via-fuchsia-400 to-sky-400 bg-clip-text text-transparent">Send a gift.</span></h3>
                <p class="relative mx-auto mt-5 max-w-md text-neutral-300">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn relative mt-9 inline-flex items-center justify-center rounded-full bg-white px-9 py-4 text-sm font-black text-neutral-950 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-xs font-bold uppercase tracking-[0.3em] text-neutral-400">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
