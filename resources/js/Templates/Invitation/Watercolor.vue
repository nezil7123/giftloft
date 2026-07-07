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
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#fdfbf7] p-10 text-center shadow-2xl ring-1 ring-neutral-200">
        <!-- soft watercolor washes -->
        <div class="pointer-events-none absolute -left-10 -top-10 h-48 w-48 rounded-full bg-rose-300/30 blur-2xl"></div>
        <div class="pointer-events-none absolute -right-12 top-1/3 h-52 w-52 rounded-full bg-sky-300/25 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-10 left-1/4 h-48 w-48 rounded-full bg-amber-300/25 blur-2xl"></div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.5),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-neutral-500">{{ typeLabel }}</p>
            <h1 data-card class="mt-6 font-serif text-5xl font-medium leading-[0.95]">
                <span class="bg-gradient-to-br from-rose-500 via-violet-500 to-sky-500 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
            </h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-neutral-500">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 h-1 w-20 rounded-full bg-gradient-to-r from-rose-400 via-violet-400 to-sky-400"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-neutral-700">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-neutral-400">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-neutral-400">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
