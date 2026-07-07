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
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#f3f6ee] p-10 text-center shadow-2xl ring-1 ring-emerald-700/15">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.5),transparent_45%)]"></div>

        <!-- botanical corners -->
        <span class="pointer-events-none absolute left-4 top-4 text-2xl opacity-40">🌿</span>
        <span class="pointer-events-none absolute right-4 top-4 -scale-x-100 text-2xl opacity-40">🌿</span>
        <span class="pointer-events-none absolute bottom-4 left-4 -scale-y-100 text-2xl opacity-40">🌿</span>
        <span class="pointer-events-none absolute bottom-4 right-4 -scale-100 text-2xl opacity-40">🌿</span>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-emerald-700/80">{{ typeLabel }}</p>
            <div data-card class="mx-auto mt-6 h-px w-16 bg-emerald-700/30"></div>
            <h1 data-card class="mt-6 font-serif text-5xl font-medium leading-[0.95] text-emerald-800">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-emerald-700/80">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 h-px w-16 bg-emerald-700/30"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#3f5a45]">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-emerald-700/60">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-emerald-700/70">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
