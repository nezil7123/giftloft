<script setup>
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="w-full max-w-md overflow-hidden rounded-[2rem] shadow-2xl">
        <div class="bg-gradient-to-br from-violet-600 via-fuchsia-600 to-rose-500 p-10 text-white">
            <p class="text-xs font-black uppercase tracking-[0.4em] text-white/70">You're invited</p>
            <p class="mt-8 text-sm font-bold uppercase tracking-[0.3em] text-white/60">{{ typeLabel }}</p>
            <h1 class="mt-2 text-5xl font-black leading-[0.95] tracking-tight">{{ hosts || event.title }}</h1>
            <p v-if="tagline" class="mt-4 text-lg font-semibold text-white/85">{{ tagline }}</p>
        </div>
        <div class="bg-neutral-950 p-8 text-white">
            <div v-if="event.starts_at" class="flex items-center gap-3">
                <span class="text-2xl">📅</span>
                <p class="font-semibold">{{ fmtFull(event.starts_at) }}</p>
            </div>
            <div v-if="location" class="mt-4 flex items-center gap-3">
                <span class="text-2xl">📍</span>
                <p class="font-semibold">{{ location }}</p>
            </div>
            <p class="mt-8 text-sm text-white/50">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
