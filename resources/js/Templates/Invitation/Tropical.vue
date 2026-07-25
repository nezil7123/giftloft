<script setup>
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const petals = Array.from({ length: 6 }, (_, i) => ({ left: (i * 41) % 100, delay: (i % 4) * 1.3, dur: 7 + (i % 3), size: 6 + (i % 3) * 2 }));
</script>

<template>
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#0c1f14] p-10 text-center shadow-2xl ring-1 ring-lime-300/20">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(132,204,22,0.2),transparent_60%)]"></div>
        <div class="pointer-events-none absolute inset-0">
            <span v-for="(p,i) in petals" :key="i" class="gl-fall absolute top-[-5%] rounded-[50%_0_50%_0] bg-fuchsia-300/40"
                :style="`left:${p.left}%;width:${p.size}px;height:${p.size}px;animation-delay:${p.delay}s;animation-duration:${p.dur}s`"></span>
        </div>
        <svg viewBox="0 0 60 60" fill="none" class="pointer-events-none absolute -left-3 -top-3 h-20 w-20 text-lime-300/25">
            <path d="M30 55 C30 40 30 28 30 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M30 20 C18 16 8 20 7 30 C6 38 16 40 30 26" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M30 32 C42 28 52 32 53 42 C54 50 44 52 30 38" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        <svg viewBox="0 0 60 60" fill="none" class="pointer-events-none absolute -bottom-3 -right-3 h-20 w-20 text-fuchsia-300/30">
            <circle cx="30" cy="30" r="4" fill="currentColor"/>
            <path d="M30 30 C30 30 20 22 24 14 C27 8 33 8 34 15 C35 22 30 30 30 30 Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
            <path d="M30 30 C30 30 40 22 36 14 C33 8 27 8 26 15 C25 22 30 30 30 30 Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
            <path d="M30 30 C30 30 22 38 15 35 C9 32 10 26 17 25 C24 24 30 30 30 30 Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
            <path d="M30 30 C30 30 38 38 45 35 C51 32 50 26 43 25 C36 24 30 30 30 30 Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(132,204,22,0.3),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-lime-300/90">{{ typeLabel }}</p>
            <h1 data-card class="mt-6 font-serif text-5xl font-medium leading-[0.95]">
                <span class="bg-gradient-to-r from-lime-200 via-emerald-200 to-teal-200 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
            </h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-emerald-200/85">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 h-px w-16 bg-lime-300/40"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-emerald-100">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-lime-300/70">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-emerald-300/70">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
