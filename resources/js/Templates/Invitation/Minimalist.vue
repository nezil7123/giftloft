<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="gl-fade gl-grain relative w-full max-w-md bg-white p-12 text-center shadow-2xl ring-1 ring-neutral-200">
        <p class="text-[10px] font-semibold uppercase tracking-[0.5em] text-neutral-400">{{ typeLabel }}</p>
        <div class="gl-grow mx-auto mt-8 h-px w-8 bg-neutral-900"></div>
        <h1 class="gl-enter gl-d1 mt-8 text-3xl font-light uppercase tracking-[0.15em] text-neutral-950">{{ hosts || event.title }}</h1>
        <p v-if="tagline" class="gl-enter gl-d2 mt-4 text-sm text-neutral-500">{{ tagline }}</p>
        <div class="gl-grow mx-auto mt-8 h-px w-8 bg-neutral-900"></div>
        <p v-if="event.starts_at" class="gl-enter gl-d3 mt-8 text-xs uppercase tracking-[0.25em] text-neutral-700">{{ fmtFull(event.starts_at) }}</p>
        <p v-if="location" class="gl-enter gl-d3 mt-2 text-xs uppercase tracking-[0.25em] text-neutral-400">{{ location }}</p>
        <p class="mt-10 text-[10px] uppercase tracking-[0.3em] text-neutral-300">{{ event.user?.name }}</p>
    </div>
</template>
