<script setup>
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const day = computed(() => (props.event.starts_at ? String(new Date(props.event.starts_at).getDate()).padStart(2, '0') : ''));
const month = computed(() => (props.event.starts_at ? new Date(props.event.starts_at).toLocaleDateString(undefined, { month: 'long' }).toUpperCase() : ''));
const year = computed(() => (props.event.starts_at ? new Date(props.event.starts_at).getFullYear() : ''));
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden bg-white p-10 text-left shadow-2xl ring-1 ring-neutral-200">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] bg-[radial-gradient(circle,rgba(0,0,0,0.045),transparent_45%)]"></div>

        <div class="relative">
            <div data-card class="flex items-center justify-between border-b-2 border-neutral-900 pb-3">
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-neutral-900">{{ typeLabel }}</p>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-neutral-400">Nº 001</p>
            </div>

            <h1 data-card class="mt-8 font-serif text-6xl font-medium uppercase leading-[0.9] tracking-tight text-neutral-950">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-5 text-sm font-medium uppercase tracking-[0.2em] text-neutral-500">{{ tagline }}</p>

            <div v-if="event.starts_at" data-card class="mt-10 flex items-end gap-4">
                <span class="font-serif text-8xl font-medium leading-none text-neutral-950">{{ day }}</span>
                <div class="pb-2">
                    <p class="text-sm font-black uppercase tracking-[0.3em] text-neutral-900">{{ month }}</p>
                    <p class="text-sm font-black tracking-[0.3em] text-neutral-400">{{ year }}</p>
                </div>
            </div>

            <div data-card class="mt-8 space-y-1 border-t border-neutral-200 pt-5">
                <p v-if="event.starts_at" class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-900">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" class="text-xs uppercase tracking-[0.2em] text-neutral-500">{{ location }}</p>
            </div>

            <p data-card class="mt-8 text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">Hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
