<script setup>
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const sealInitial = computed(() => (props.event.user?.name || hosts.value || 'G').trim()[0]?.toUpperCase() ?? 'G');
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden rounded-sm bg-[#f6ecd9] p-10 text-center shadow-2xl"
        style="background-image: radial-gradient(circle at 20% 15%, rgba(180,146,96,0.14), transparent 45%), radial-gradient(circle at 85% 80%, rgba(180,146,96,0.18), transparent 40%);">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(120,80,30,0.07),transparent_45%)]"></div>
        <!-- Deckled edge -->
        <div class="pointer-events-none absolute inset-2 border border-dashed border-amber-800/30"></div>

        <div class="relative">
            <p data-card class="text-[10px] font-semibold uppercase tracking-[0.45em] text-amber-800/80">By candlelight, you are invited</p>
            <p data-card class="mt-5 font-serif text-sm italic text-amber-900/70">to a {{ typeLabel.toLowerCase() }} in honour of</p>

            <h1 data-card class="mt-4 font-serif text-5xl font-medium leading-[0.95] text-amber-950">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-4 font-serif text-base italic text-amber-800/80">{{ tagline }}</p>

            <div data-card class="mx-auto mt-6 flex items-center justify-center gap-3 text-amber-800/60">
                <span class="h-px w-14 bg-amber-800/30"></span>
                <span class="font-serif italic">est. {{ new Date(event.starts_at ?? Date.now()).getFullYear() }}</span>
                <span class="h-px w-14 bg-amber-800/30"></span>
            </div>

            <p v-if="event.starts_at" data-card class="mt-6 font-serif text-base text-amber-950">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1.5 text-[11px] uppercase tracking-[0.3em] text-amber-800/70">{{ location }}</p>

            <!-- Wax seal -->
            <div data-card class="mt-8 flex items-center justify-center">
                <div class="relative flex h-16 w-16 items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-rose-700 via-red-800 to-rose-900 shadow-lg" style="border-radius: 52% 48% 55% 45% / 48% 55% 45% 52%;"></div>
                    <div class="absolute inset-1.5 rounded-full border border-rose-300/30" style="border-radius: 52% 48% 55% 45% / 48% 55% 45% 52%;"></div>
                    <span class="relative font-serif text-2xl text-rose-100">{{ sealInitial }}</span>
                </div>
            </div>
            <p data-card class="mt-4 font-serif text-xs italic text-amber-800/60">{{ event.user?.name }}</p>
        </div>
    </div>
</template>
