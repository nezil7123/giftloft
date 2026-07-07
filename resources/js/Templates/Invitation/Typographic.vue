<script setup>
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const day = computed(() => (props.event.starts_at ? String(new Date(props.event.starts_at).getDate()).padStart(2, '0') : ''));
const month = computed(() => (props.event.starts_at ? new Date(props.event.starts_at).toLocaleDateString(undefined, { month: 'short' }).toUpperCase() : ''));
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden bg-[#fce803] p-9 text-left text-neutral-950 shadow-2xl">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.5),transparent_45%)]"></div>

        <div class="relative">
            <div data-card class="flex items-center justify-between border-b-4 border-neutral-950 pb-3">
                <p class="text-[11px] font-black uppercase tracking-[0.3em]">{{ typeLabel }}</p>
                <p v-if="day" class="text-[11px] font-black uppercase tracking-[0.3em]">{{ day }} · {{ month }}</p>
            </div>

            <h1 data-card class="mt-6 text-6xl font-black uppercase leading-[0.85] tracking-tighter">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-5 text-lg font-bold uppercase leading-tight">{{ tagline }}</p>

            <div data-card class="mt-8 space-y-1 border-t-4 border-neutral-950 pt-4">
                <p v-if="event.starts_at" class="text-sm font-black uppercase tracking-wide">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" class="text-sm font-bold uppercase tracking-wide">{{ location }}</p>
            </div>
            <p data-card class="mt-8 text-[11px] font-black uppercase tracking-[0.3em]">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
