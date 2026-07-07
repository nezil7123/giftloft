<script setup>
import { ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const marble = 'radial-gradient(120% 80% at 20% 10%, rgba(255,255,255,0.95), transparent 60%), radial-gradient(90% 60% at 85% 30%, rgba(214,203,178,0.3), transparent 55%), radial-gradient(100% 70% at 60% 100%, rgba(200,188,160,0.25), transparent 60%), #f7f5f0';
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-[2rem] p-10 text-center shadow-2xl ring-1 ring-amber-600/20" :style="`background:${marble}`">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.6),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="text-xs font-semibold uppercase tracking-[0.4em] text-amber-700/80">{{ typeLabel }}</p>
            <div data-card class="mx-auto mt-6 flex items-center justify-center gap-3">
                <span class="h-px w-12 bg-gradient-to-r from-transparent to-amber-600/50"></span>
                <span class="text-amber-600">❖</span>
                <span class="h-px w-12 bg-gradient-to-l from-transparent to-amber-600/50"></span>
            </div>
            <h1 data-card class="mt-6 font-serif text-5xl font-medium leading-[0.95] text-[#33302a] gl-shimmer" style="--gl-shimmer-color:#9c7a3c">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-[#6a5c3e]">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 h-px w-16 bg-amber-600/30"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#43402e]">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-amber-700/60">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-amber-700/70">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
