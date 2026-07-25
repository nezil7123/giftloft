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
    <div ref="card" class="relative flex w-full max-w-md overflow-hidden rounded-[2rem] shadow-2xl ring-1 ring-amber-600/20">
        <!-- vertical accent rail -->
        <div class="relative flex w-14 shrink-0 items-center justify-center bg-gradient-to-b from-amber-700 to-amber-500 sm:w-16">
            <span class="whitespace-nowrap text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-50" style="writing-mode: vertical-rl; transform: rotate(180deg);">{{ typeLabel }}</span>
        </div>

        <div class="relative flex-1 p-8 sm:p-10" :style="`background:${marble}`">
            <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(255,255,255,0.6),transparent_45%)]"></div>

            <div class="relative">
                <span data-card class="text-amber-600">❖</span>
                <h1 data-card class="mt-4 font-serif text-4xl font-medium leading-[0.95]">
                    <span class="gl-shimmer" style="--gl-shimmer-color:#9c7a3c">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" data-card class="mt-3 font-serif text-base italic text-[#6a5c3e]">{{ tagline }}</p>
                <div data-card class="mt-6 h-px w-14 bg-amber-600/30"></div>
                <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium text-[#43402e]">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" data-card class="mt-1 text-xs uppercase tracking-[0.2em] text-amber-700/60">{{ location }}</p>
                <p data-card class="mt-8 font-serif text-xs italic text-amber-700/70">With love, {{ event.user?.name }}</p>
            </div>
        </div>
    </div>
</template>
