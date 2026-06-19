<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="w-full max-w-md bg-[#0e1530] p-3 shadow-2xl">
        <!-- gold deco frame -->
        <div class="relative border-2 border-amber-300/60 p-10 text-center">
            <div class="pointer-events-none absolute left-3 top-3 h-5 w-5 border-l-2 border-t-2 border-amber-300"></div>
            <div class="pointer-events-none absolute right-3 top-3 h-5 w-5 border-r-2 border-t-2 border-amber-300"></div>
            <div class="pointer-events-none absolute bottom-3 left-3 h-5 w-5 border-b-2 border-l-2 border-amber-300"></div>
            <div class="pointer-events-none absolute bottom-3 right-3 h-5 w-5 border-b-2 border-r-2 border-amber-300"></div>

            <p class="text-[10px] font-semibold uppercase tracking-[0.5em] text-amber-300">{{ typeLabel }}</p>
            <p class="mt-6 text-2xl text-amber-300">✦</p>
            <h1 class="mt-4 font-serif text-4xl font-bold leading-tight text-white">{{ hosts || event.title }}</h1>
            <p v-if="tagline" class="mt-3 font-serif text-base italic text-amber-200/90">{{ tagline }}</p>

            <div class="mx-auto mt-6 flex items-center justify-center gap-2">
                <span class="h-px w-10 bg-amber-300/50"></span>
                <span class="text-amber-300">◆</span>
                <span class="h-px w-10 bg-amber-300/50"></span>
            </div>

            <p v-if="event.starts_at" class="mt-6 text-sm font-medium tracking-wide text-neutral-200">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" class="mt-1 text-[11px] uppercase tracking-[0.25em] text-amber-300/70">{{ location }}</p>
            <p class="mt-8 font-serif text-xs italic text-neutral-400">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
