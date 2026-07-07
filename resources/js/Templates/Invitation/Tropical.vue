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
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#0c1f14] p-10 text-center shadow-2xl ring-1 ring-lime-300/20">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(132,204,22,0.2),transparent_60%)]"></div>
        <span class="pointer-events-none absolute -left-4 -top-4 text-6xl opacity-25">🌴</span>
        <span class="pointer-events-none absolute -bottom-5 -right-3 text-6xl opacity-25">🌺</span>
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
