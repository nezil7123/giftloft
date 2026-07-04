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

const marquee = computed(() => [hosts.value || props.event.title, '✿', fmtDate(props.event.starts_at), '✿', location.value || 'You’re invited', '✿']);
const stack = computed(() => [coverPhoto.value, ...gallery.value].filter(Boolean).slice(0, 3));
</script>

<template>
    <div ref="root" class="gl-grain relative overflow-hidden bg-[#fbf6ec]">
        <!-- floating doodles -->
        <div class="gl-float pointer-events-none absolute right-6 top-28 text-5xl opacity-30">✨</div>
        <div class="gl-float pointer-events-none absolute left-8 top-64 text-4xl opacity-25" style="animation-delay:2s">💛</div>
        <div class="gl-float pointer-events-none absolute right-12 top-[60%] text-4xl opacity-20" style="animation-delay:1s">🎈</div>

        <!-- HERO — photo stack fan -->
        <section class="relative mx-auto max-w-4xl px-6 pb-12 pt-32 text-center sm:px-10">
            <span class="gl-pop inline-block -rotate-2 rounded-md bg-amber-200/70 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-amber-800 shadow-sm">{{ typeLabel }}</span>
            <h1 class="mt-7 overflow-hidden">
                <span class="gl-clip gl-display block font-serif font-black text-neutral-900">{{ hosts || event.title }}</span>
            </h1>
            <p v-if="tagline" class="gl-enter gl-d2 mt-5 font-serif text-2xl italic text-neutral-500">{{ tagline }}</p>

            <!-- The stack: hover to fan out -->
            <div v-if="stack.length" class="stack group relative mx-auto mt-14 h-[24rem] w-full max-w-md sm:h-[27rem]">
                <div v-for="(p,i) in stack" :key="i"
                    class="stack-card absolute left-1/2 top-0 w-72 -translate-x-1/2 rounded-sm border-[10px] border-white bg-white pb-8 shadow-2xl transition-all duration-700 sm:w-80"
                    :style="`z-index:${10-i}`" :data-pos="i">
                    <div class="absolute -top-3.5 left-1/2 h-6 w-24 -translate-x-1/2 rotate-1 bg-amber-200/80"></div>
                    <div class="gl-photo overflow-hidden">
                        <img :src="p" alt="" class="aspect-[4/3] w-full object-cover" :class="{ 'gl-ken': i === 0 }" />
                    </div>
                    <p class="mt-3 text-center font-serif text-base italic text-neutral-500">{{ i === 0 ? fmtDate(event.starts_at) : '♡' }}</p>
                </div>
            </div>
            <p class="gl-enter gl-d3 mt-4 text-xs uppercase tracking-[0.3em] text-neutral-400">hover the photos ↑</p>

            <div v-if="countdown" class="mt-12 inline-flex gap-3">
                <div v-for="(u,idx) in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l"
                    class="gl-float-soft flex h-20 w-16 flex-col items-center justify-center rounded-lg border-4 border-white bg-rose-100 shadow-md sm:w-20"
                    :class="idx % 2 ? 'rotate-2' : '-rotate-2'" :style="`animation-delay:${idx*0.5}s`">
                    <span class="text-3xl font-black tabular-nums text-rose-500">{{ String(u.v).padStart(2,'0') }}</span>
                    <span class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ u.l }}</span>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="my-6 overflow-hidden border-y-4 border-dashed border-rose-300 py-3">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 font-serif text-2xl" :class="m === '✿' ? 'text-rose-400' : 'text-neutral-800'">{{ m }}</span>
                </span>
            </div>
        </div>

        <section class="relative mx-auto max-w-3xl space-y-8 px-6 pb-20 sm:px-10">
            <div v-if="event.description" data-reveal class="rotate-[0.5deg] rounded-xl border-[10px] border-white bg-white p-8 shadow-xl transition hover:rotate-0 sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">Our story</p>
                <p class="mt-4 whitespace-pre-line font-serif text-xl leading-8 text-neutral-700">{{ event.description }}</p>
            </div>

            <div v-if="schedule.length" data-reveal class="-rotate-[0.5deg] rounded-xl border-[10px] border-white bg-white p-8 shadow-xl transition hover:rotate-0 sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">The day</p>
                <ol class="mt-6 space-y-4">
                    <li v-for="(s,i) in schedule" :key="i" class="flex gap-4">
                        <span class="shrink-0 -rotate-2 rounded-md bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ s.time }}</span>
                        <div>
                            <p class="font-serif text-lg font-bold text-neutral-900">{{ s.title }}</p>
                            <p v-if="s.detail" class="text-sm text-neutral-500">{{ s.detail }}</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto w-fit rotate-1 rounded-md bg-white px-7 py-5 text-center shadow-lg ring-1 ring-neutral-200">
                <p class="text-xs uppercase tracking-widest text-neutral-400">Dress code</p>
                <p class="mt-1 font-serif text-2xl text-neutral-800">{{ dressCode }}</p>
            </div>

            <!-- The place -->
            <div v-if="event.venue || venueNote || venuePhoto || travel || stay" data-reveal class="-rotate-[0.5deg] rounded-xl border-[10px] border-white bg-white p-8 shadow-xl transition hover:rotate-0 sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">The place</p>
                <p class="mt-3 font-serif text-2xl font-bold text-neutral-900">📍 {{ event.venue || location }}</p>
                <p v-if="venueNote" class="mt-3 font-serif text-lg leading-8 text-neutral-600">{{ venueNote }}</p>
                <div v-if="venuePhoto" class="mx-auto mt-5 max-w-md -rotate-1 overflow-hidden rounded-lg border-[6px] border-white shadow-md">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover" />
                </div>
                <div data-fx="batch" class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div v-if="travel" class="-rotate-1 rounded-lg bg-amber-50 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-700">🚗 Getting there</p>
                        <p class="mt-2 text-sm leading-7 text-neutral-600">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="rotate-1 rounded-lg bg-rose-50 p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-rose-600">🏡 Where to stay</p>
                        <p class="mt-2 text-sm leading-7 text-neutral-600">{{ stay }}</p>
                    </div>
                </div>
                <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="mt-6 inline-block -rotate-1 rounded-md bg-neutral-900 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition hover:rotate-0 hover:scale-105">Find it on the map ✂️</a>
            </div>

            <!-- Polaroid gallery -->
            <div v-if="gallery.length" data-reveal class="rotate-[0.5deg] rounded-xl border-[10px] border-white bg-white p-6 shadow-xl sm:p-8">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">Our snapshots</p>
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <figure v-for="(p,i) in gallery" :key="i"
                        class="gl-photo gl-float-soft rounded-sm border-[6px] border-white bg-white pb-6 shadow-lg transition hover:rotate-0 hover:scale-[1.04]"
                        :class="i % 2 ? 'rotate-2' : '-rotate-2'" :style="`animation-delay:${(i % 4) * 0.4}s`">
                        <img :src="p" alt="" loading="lazy" class="aspect-square w-full object-cover" />
                        <figcaption class="pt-2 text-center font-serif text-xs italic text-neutral-400">♡</figcaption>
                    </figure>
                </div>
            </div>

            <div v-if="faqs.length" data-reveal class="rotate-[0.5deg] rounded-xl border-[10px] border-white bg-white p-8 shadow-xl sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">Questions</p>
                <div class="mt-5 space-y-4">
                    <div v-for="(f,i) in faqs" :key="i">
                        <p class="font-serif text-lg font-bold text-neutral-900">{{ f.question }}</p>
                        <p class="mt-1 leading-7 text-neutral-600">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <div data-reveal="zoom" class="rounded-2xl bg-rose-500 p-8 text-center text-white shadow-xl sm:p-12">
                <p class="font-serif text-4xl">Our Wishlist 🎁</p>
                <p class="mx-auto mt-3 max-w-md text-rose-100">{{ rsvpNote || 'A few things that would make us smile.' }}</p>
                <Link :href="registryUrl" class="gl-btn mt-7 inline-flex rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-rose-600 transition hover:scale-105">Open the wishlist</Link>
            </div>

            <p class="text-center font-serif text-base italic text-neutral-400">Made with love by {{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>

<style scoped>
/* Photo stack — cards fan out on hover */
.stack-card[data-pos="0"] { transform: translateX(-50%) rotate(-2deg); }
.stack-card[data-pos="1"] { transform: translateX(-50%) rotate(4deg) translateY(10px); }
.stack-card[data-pos="2"] { transform: translateX(-50%) rotate(-7deg) translateY(20px); }
.stack:hover .stack-card[data-pos="0"] { transform: translateX(-50%) rotate(0deg) translateY(-8px); }
.stack:hover .stack-card[data-pos="1"] { transform: translateX(-14%) rotate(9deg) translateY(6px); }
.stack:hover .stack-card[data-pos="2"] { transform: translateX(-86%) rotate(-10deg) translateY(6px); }

@media (prefers-reduced-motion: reduce) {
    .stack-card, .stack:hover .stack-card { transition: none; }
}
</style>
