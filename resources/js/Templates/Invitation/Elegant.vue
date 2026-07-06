<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="gl-pop gl-grain relative w-full max-w-md overflow-hidden rounded-[2rem] bg-[#faf7f0] p-10 text-center shadow-2xl ring-1 ring-amber-200/60">
        <div class="pointer-events-none absolute inset-4 rounded-[1.5rem] border border-amber-400/40"></div>
        <!-- corner sparkles -->
        <span class="gl-twinkle absolute left-7 top-7 text-amber-400">✦</span>
        <span class="gl-twinkle absolute right-7 top-7 text-amber-400" style="animation-delay:.8s">✦</span>
        <span class="gl-twinkle absolute bottom-7 left-7 text-amber-400" style="animation-delay:1.2s">✦</span>
        <span class="gl-twinkle absolute bottom-7 right-7 text-amber-400" style="animation-delay:1.6s">✦</span>

        <div class="relative">
            <p class="font-serif text-xs uppercase tracking-[0.4em] text-amber-600">You're invited</p>
            <div class="gl-grow mx-auto mt-6 h-px w-16 bg-amber-400/60"></div>
            <p class="mt-6 font-serif text-sm uppercase tracking-[0.3em] text-neutral-500">{{ typeLabel }}</p>
            <h1 class="mt-3 font-serif text-5xl font-medium leading-[0.95] text-amber-700 gl-shimmer">{{ hosts || event.title }}</h1>
            <p v-if="tagline" class="mt-3 font-serif text-lg italic text-amber-700">{{ tagline }}</p>
            <div class="gl-grow mx-auto mt-7 h-px w-16 bg-amber-400/60"></div>
            <p v-if="event.starts_at" class="mt-6 text-sm font-medium text-neutral-700">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" class="mt-1 text-xs uppercase tracking-[0.2em] text-neutral-500">{{ location }}</p>
            <p class="mt-8 font-serif text-xs italic text-neutral-500">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
