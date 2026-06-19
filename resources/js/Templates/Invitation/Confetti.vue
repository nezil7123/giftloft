<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-gradient-to-br from-sky-400 via-fuchsia-400 to-amber-300 p-1.5 shadow-2xl">
        <div class="relative overflow-hidden rounded-[1.6rem] bg-white p-10 text-center">
            <!-- confetti dots -->
            <div class="pointer-events-none absolute left-5 top-6 h-3 w-3 rounded-full bg-rose-400"></div>
            <div class="pointer-events-none absolute right-8 top-10 h-2 w-2 rounded-full bg-sky-400"></div>
            <div class="pointer-events-none absolute left-10 top-16 h-2 w-2 rotate-45 bg-amber-400"></div>
            <div class="pointer-events-none absolute right-6 bottom-12 h-3 w-3 rounded-full bg-fuchsia-400"></div>
            <div class="pointer-events-none absolute left-7 bottom-8 h-2 w-2 rotate-45 bg-emerald-400"></div>

            <div class="relative">
                <p class="text-4xl">🎉</p>
                <p class="mt-3 text-xs font-black uppercase tracking-[0.3em] text-fuchsia-500">{{ typeLabel }}</p>
                <h1 class="mt-3 text-4xl font-black leading-tight text-neutral-900">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="mt-3 text-base font-bold text-sky-500">{{ tagline }}</p>

                <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-neutral-900 px-5 py-2 text-sm font-bold text-white">
                    <span v-if="event.starts_at">📅 {{ fmtFull(event.starts_at) }}</span>
                </div>
                <p v-if="location" class="mt-3 text-sm font-semibold text-neutral-500">📍 {{ location }}</p>
                <p class="mt-8 text-xs font-bold uppercase tracking-widest text-neutral-300">Hosted by {{ event.user?.name }}</p>
            </div>
        </div>
    </div>
</template>
