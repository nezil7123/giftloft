<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-gradient-to-b from-rose-50 to-pink-100 p-10 text-center shadow-2xl ring-1 ring-rose-200">
        <!-- botanical corners -->
        <div class="pointer-events-none absolute -left-6 -top-6 text-7xl opacity-30 rotate-12">🌸</div>
        <div class="pointer-events-none absolute -bottom-6 -right-6 text-7xl opacity-30 -rotate-12">🌿</div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-400">Together with family</p>
            <p class="mt-6 text-3xl">💐</p>
            <p class="mt-4 text-xs uppercase tracking-[0.3em] text-rose-400">{{ typeLabel }}</p>
            <h1 class="mt-2 font-serif text-4xl font-bold leading-tight text-rose-900">{{ hosts || event.title }}</h1>
            <p v-if="tagline" class="mt-3 text-base italic text-rose-500">{{ tagline }}</p>
            <div class="mx-auto mt-6 flex items-center justify-center gap-2 text-rose-300">
                <span class="h-px w-10 bg-rose-300"></span>🌷<span class="h-px w-10 bg-rose-300"></span>
            </div>
            <p v-if="event.starts_at" class="mt-5 text-sm font-medium text-rose-800">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" class="mt-1 text-xs uppercase tracking-[0.2em] text-rose-500">{{ location }}</p>
            <p class="mt-8 text-xs italic text-rose-400">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
