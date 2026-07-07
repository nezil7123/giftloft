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
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-[2rem] border-2 border-[#4a2e17] bg-[#f6ead3] p-10 text-center shadow-2xl">
        <div class="pointer-events-none absolute inset-0 bg-[repeating-conic-gradient(#e8804a_0deg_18deg,transparent_18deg_36deg)] opacity-[0.05]"></div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(244,184,96,0.4),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="inline-block rounded-full bg-[#e8804a] px-4 py-1.5 text-[11px] font-black uppercase tracking-[0.3em] text-[#f6ead3]">{{ typeLabel }}</p>
            <h1 data-card class="mt-6 font-serif text-5xl font-black uppercase leading-[0.85] text-[#c14a1f]">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-4 text-lg font-bold text-[#8a5a2a]">{{ tagline }}</p>
            <div data-card class="mx-auto mt-6 h-1 w-20 rounded-full bg-[#c14a1f]"></div>
            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-black uppercase tracking-wide text-[#4a2e17]">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1 text-xs font-bold uppercase tracking-[0.2em] text-[#a06a35]">{{ location }}</p>
            <p data-card class="mt-8 text-[11px] font-black uppercase tracking-[0.3em] text-[#c14a1f]">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
