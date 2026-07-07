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
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#faf6ec] p-10 text-center shadow-2xl ring-1 ring-amber-500/30">
        <div class="pointer-events-none absolute inset-4 rounded-[1.5rem] border border-amber-500/40"></div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(202,166,92,0.35),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-amber-700/80">{{ typeLabel }}</p>
            <div data-card class="mx-auto mt-6 flex items-center justify-center gap-3">
                <span class="h-px w-14 bg-gradient-to-r from-transparent to-amber-500/60"></span>
                <span class="text-amber-600">✦</span>
                <span class="h-px w-14 bg-gradient-to-l from-transparent to-amber-500/60"></span>
            </div>
            <h1 data-card class="mt-6 font-serif text-5xl font-medium leading-[0.95] text-amber-800 gl-shimmer" style="--gl-shimmer-color:#a67c2e">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-amber-800/80">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 flex items-center justify-center gap-3">
                <span class="h-px w-14 bg-gradient-to-r from-transparent to-amber-500/60"></span>
                <span class="text-amber-600">✦</span>
                <span class="h-px w-14 bg-gradient-to-l from-transparent to-amber-500/60"></span>
            </div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#5a4d33]">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-amber-700/60">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-amber-700/70">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
