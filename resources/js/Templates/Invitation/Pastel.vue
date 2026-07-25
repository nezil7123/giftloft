<script setup>
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-gradient-to-br from-rose-200 via-violet-200 to-sky-200 p-1.5 shadow-2xl">
        <div class="relative overflow-hidden rounded-[1.6rem] bg-white/70 p-10 text-center backdrop-blur-sm">
            <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.6),transparent_45%)]"></div>
            <div class="relative">
                <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-violet-500/80">{{ typeLabel }}</p>
                <svg data-card viewBox="0 0 40 40" fill="none" class="mx-auto mt-5 h-7 w-7 text-violet-400/80">
                    <path d="M20 20 C20 20 8 14 8 8 C8 4 12 3 15 6 C18 9 20 16 20 20 Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M20 20 C20 20 32 14 32 8 C32 4 28 3 25 6 C22 9 20 16 20 20 Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <circle cx="20" cy="21" r="2.4" fill="currentColor"/>
                </svg>
                <h1 data-card class="mt-4 font-serif text-5xl font-medium leading-[0.95]">
                    <span class="bg-gradient-to-r from-rose-400 via-violet-400 to-sky-400 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-violet-500/80">{{ tagline }}</p>
                <div data-card class="mx-auto mt-7 h-1 w-16 rounded-full bg-gradient-to-r from-rose-300 to-sky-300"></div>
                <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#4d4168]">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-violet-400/70">{{ location }}</p>
                <p data-card class="mt-8 font-serif text-xs italic text-violet-400/70">With love, {{ event.user?.name }}</p>
            </div>
        </div>
    </div>
</template>
