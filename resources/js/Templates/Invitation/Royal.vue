<script setup>
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useCardFx } from '../useCardFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, location, fmtFull } = useEventDisplay(() => props.event);
const card = ref(null);
useCardFx(card);

const monogram = computed(() => {
    const names = (hosts.value || props.event.title || '').split(/\s*(?:&|and|·)\s*/i).filter(Boolean);
    const initials = names.map((n) => n.trim()[0]?.toUpperCase()).filter(Boolean).slice(0, 2);
    return initials.length > 1 ? initials.join('·') : (initials[0] ?? '❦');
});
</script>

<template>
    <div ref="card" class="relative w-full max-w-md overflow-hidden bg-[#06231c] p-3 shadow-2xl">
        <div data-card-shine class="pointer-events-none absolute inset-[-40%] z-10 bg-[radial-gradient(circle,rgba(251,224,172,0.10),transparent_45%)]"></div>

        <div class="gl-grain relative border border-amber-200/50 p-9 text-center">
            <div class="pointer-events-none absolute inset-1.5 border border-amber-200/25"></div>

            <p data-card class="text-[10px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">{{ typeLabel }}</p>

            <div data-card class="relative mx-auto mt-6 flex h-20 w-20 items-center justify-center">
                <svg viewBox="0 0 100 100" fill="none" class="absolute inset-0 text-amber-300/70">
                    <circle cx="50" cy="50" r="46" stroke="currentColor" stroke-width="1"/>
                    <path d="M20 68 q-9 -20 4 -38 M80 68 q9 -20 -4 -38" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                </svg>
                <span class="font-serif text-2xl text-amber-100">{{ monogram }}</span>
            </div>

            <h1 data-card class="mt-5 font-serif text-4xl font-medium leading-tight text-amber-50">{{ hosts || event.title }}</h1>
            <p v-if="tagline" data-card class="mt-3 font-serif text-base italic text-amber-200/80">{{ tagline }}</p>

            <div data-card class="mx-auto mt-6 flex items-center justify-center gap-2 text-amber-300/70">
                <span class="h-px w-10 bg-amber-300/40"></span>
                <span>❦</span>
                <span class="h-px w-10 bg-amber-300/40"></span>
            </div>

            <p v-if="event.starts_at" data-card class="mt-6 text-sm font-medium tracking-wide text-emerald-50">{{ fmtFull(event.starts_at) }}</p>
            <p v-if="location" data-card class="mt-1.5 text-[11px] uppercase tracking-[0.25em] text-amber-200/70">{{ location }}</p>
            <p data-card class="mt-8 font-serif text-xs italic text-emerald-100/50">Graciously hosted by {{ event.user?.name }}</p>
        </div>
    </div>
</template>
