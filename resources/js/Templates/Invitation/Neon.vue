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
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-3xl bg-[#0a0812] p-10 text-center shadow-2xl ring-1 ring-fuchsia-500/40">
        <!-- Glow field -->
        <div class="pointer-events-none absolute inset-0">
            <div class="gl-pulse absolute -left-16 -top-16 h-48 w-48 rounded-full bg-fuchsia-600/30 blur-3xl"></div>
            <div class="gl-pulse absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-cyan-500/30 blur-3xl" style="animation-delay:1s"></div>
        </div>
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(232,121,249,0.12),transparent_45%)]"></div>

        <div class="relative">
            <p data-card class="inline-flex rounded-full border border-cyan-400/60 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.4em] text-cyan-300" style="text-shadow: 0 0 12px rgba(34,211,238,0.8)">{{ typeLabel }}</p>

            <h1 data-card class="mt-8 text-5xl font-black uppercase leading-[0.95] tracking-tight text-fuchsia-300" style="text-shadow: 0 0 16px rgba(232,121,249,0.75), 0 0 46px rgba(217,70,239,0.45)">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-4 text-base font-semibold text-cyan-200" style="text-shadow: 0 0 12px rgba(34,211,238,0.6)">{{ tagline }}</p>

            <div data-card class="mx-auto mt-7 h-px w-40 bg-gradient-to-r from-transparent via-fuchsia-400 to-transparent" style="box-shadow: 0 0 12px rgba(232,121,249,0.9)"></div>

            <p v-if="event.starts_at" data-card class="mt-7 text-sm font-bold tracking-wide text-white">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1.5 text-[11px] font-semibold uppercase tracking-[0.3em] text-cyan-300/80">{{ location }}</p>

            <p data-card class="mt-8 text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-500">Hosted by {{ event.user?.name }} · Be there</p>
        </div>
    </div>
</template>
