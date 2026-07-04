<script setup>
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const stars = Array.from({ length: 26 }, (_, i) => ({ top: (i * 43) % 100, left: (i * 61) % 100, delay: (i % 7) * 0.4 }));
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-3xl bg-[#050a18] p-10 text-center shadow-2xl">
        <!-- Aurora wash + pointer-tracked shine -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-1/4 top-[-20%] h-2/3 w-[150%] rotate-[-14deg] bg-gradient-to-r from-teal-400/30 via-sky-500/20 to-transparent blur-3xl"></div>
            <div class="absolute -right-1/4 bottom-[-15%] h-1/2 w-[140%] rotate-[10deg] bg-gradient-to-l from-violet-500/30 via-fuchsia-500/15 to-transparent blur-3xl"></div>
            <span v-for="(s,i) in stars" :key="i" class="gl-twinkle absolute h-px w-px rounded-full bg-white" :style="`top:${s.top}%;left:${s.left}%;animation-delay:${s.delay}s`"></span>
        </div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.10),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-[10px] font-semibold uppercase tracking-[0.5em] text-teal-300/90">{{ typeLabel }}</p>
            <p data-card class="mt-6 text-xl text-teal-200/80">✦</p>
            <h1 data-card class="mt-3 font-serif text-5xl font-medium leading-[0.95]">
                <span class="bg-gradient-to-r from-teal-200 via-sky-200 to-violet-300 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
            </h1>
            <p v-if="tagline" data-card class="mt-4 font-serif text-base italic text-sky-100/80">{{ tagline }}</p>

            <div data-card class="mx-auto mt-7 flex items-center justify-center gap-2">
                <span class="h-px w-12 bg-gradient-to-r from-transparent to-teal-300/60"></span>
                <span class="h-1.5 w-1.5 rounded-full bg-sky-300/80"></span>
                <span class="h-px w-12 bg-gradient-to-l from-transparent to-violet-300/60"></span>
            </div>

            <p v-if="event.starts_at" data-card class="mt-7 text-sm font-medium tracking-wide text-neutral-100">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1.5 text-[11px] uppercase tracking-[0.25em] text-teal-200/70">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-neutral-400">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
