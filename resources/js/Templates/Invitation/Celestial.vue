<script setup>
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const stars = Array.from({ length: 40 }, (_, i) => ({ top: (i * 37) % 100, left: (i * 53) % 100, delay: (i % 9) * 0.4, size: i % 5 === 0 ? 2 : 1 }));
</script>

<template>
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#0b0a1e] p-10 text-center shadow-2xl ring-1 ring-indigo-300/20">
        <div class="pointer-events-none absolute inset-0">
            <span v-for="(s,i) in stars" :key="i" class="gl-twinkle absolute rounded-full bg-white" :style="`top:${s.top}%;left:${s.left}%;height:${s.size}px;width:${s.size}px;animation-delay:${s.delay}s`"></span>
        </div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(139,92,246,0.3),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-violet-300/90">{{ typeLabel }}</p>
            <p data-card class="mt-5 text-3xl">🌙</p>
            <h1 data-card class="mt-4 font-serif text-5xl font-medium leading-[0.95]">
                <span class="bg-gradient-to-r from-indigo-200 via-violet-200 to-fuchsia-200 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
            </h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-indigo-200/80">{{ tagline }}</p>
            <div data-card class="mx-auto mt-6 flex items-center justify-center gap-2">
                <span class="h-px w-10 bg-violet-300/40"></span>
                <span class="gl-twinkle text-violet-300">✦</span>
                <span class="h-px w-10 bg-violet-300/40"></span>
            </div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-indigo-100">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-violet-300/70">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-indigo-300/70">Under the stars with {{ event.user?.name }}</p>
        </div>
    </div>
</template>
