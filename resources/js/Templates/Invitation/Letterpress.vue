<script setup>
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const monogram = computed(() => (hosts.value || props.event.title || '')
    .split(/[\s&]+/).filter(Boolean).slice(0, 2).map((w) => w[0]).join('').toUpperCase());
</script>

<template>
    <div ref="card" class="gl-grain relative w-full max-w-md overflow-hidden rounded-sm bg-[#f4f1ea] p-10 text-center shadow-2xl ring-1 ring-[#2b2a27]/15">
        <div class="pointer-events-none absolute inset-4 border border-[#2b2a27]/20"></div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.5),transparent_45%)]"></div>

        <div class="relative">
            <div data-card class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-[#2b2a27]/40 font-serif text-xl tracking-tight text-[#2b2a27]">{{ monogram || '·' }}</div>
            <p data-card class="mt-6 text-xs font-semibold uppercase tracking-[0.4em] text-[#8a857a]">{{ typeLabel }}</p>
            <h1 data-card class="mt-4 font-serif text-5xl font-normal leading-[0.95] text-[#2b2a27]">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-lg italic text-[#5a564d]">{{ tagline }}</p>
            <div data-card class="mx-auto mt-7 h-px w-16 bg-[#2b2a27]/30"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#3a382f]">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-[#8a857a]">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-[#9a958a]">With love, {{ event.user?.name }}</p>
        </div>
    </div>
</template>
