<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-[#f6f5ef]">
        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-10 -top-10 text-[10rem] leading-none opacity-10 select-none">🌿</div>
            <div class="pointer-events-none absolute -bottom-12 -right-8 text-[10rem] leading-none opacity-10 select-none rotate-12">🍃</div>

            <div class="relative mx-auto max-w-4xl px-6 pb-16 pt-36 text-center sm:px-10">
                <p class="font-serif text-xs uppercase tracking-[0.4em] text-emerald-700">{{ typeLabel }}</p>
                <div class="mx-auto mt-5 flex items-center justify-center gap-3 text-emerald-600/70">
                    <span class="h-px w-12 bg-emerald-600/40"></span>🌷<span class="h-px w-12 bg-emerald-600/40"></span>
                </div>
                <h1 class="mt-5 font-serif text-5xl font-bold leading-tight text-emerald-950 sm:text-7xl">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="mt-4 font-serif text-xl italic text-emerald-700">{{ tagline }}</p>

                <div v-if="event.cover_photo_url" class="mx-auto mt-10 aspect-[16/10] max-w-3xl overflow-hidden rounded-[2rem] shadow-xl ring-1 ring-emerald-900/10">
                    <img :src="event.cover_photo_url" class="h-full w-full object-cover" />
                </div>

                <p v-if="event.starts_at" class="mt-8 text-base text-emerald-900">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" class="mt-1 text-xs uppercase tracking-[0.25em] text-emerald-700/70">{{ location }}</p>

                <div v-if="countdown" class="mt-9 inline-flex gap-4">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l" class="text-center">
                        <p class="font-serif text-4xl font-bold text-emerald-800">{{ String(u.v).padStart(2,'0') }}</p>
                        <p class="mt-1 text-[10px] uppercase tracking-widest text-emerald-600/60">{{ u.l }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-3xl space-y-6 px-6 pb-20 sm:px-10">
            <div v-if="event.description" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 text-center sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.3em] text-emerald-600">Our story</p>
                <p class="mt-4 whitespace-pre-line text-lg leading-8 text-emerald-950/80">{{ event.description }}</p>
            </div>

            <div v-if="schedule.length" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 sm:p-10">
                <p class="text-center font-serif text-sm uppercase tracking-[0.3em] text-emerald-600">Schedule</p>
                <ol class="mt-6 space-y-5">
                    <li v-for="(s,i) in schedule" :key="i" class="flex items-baseline justify-center gap-4 text-center">
                        <span class="font-serif text-emerald-700">{{ s.time }}</span>
                        <span class="text-emerald-300">·</span>
                        <span>
                            <span class="font-semibold text-emerald-950">{{ s.title }}</span>
                            <span v-if="s.detail" class="block text-sm text-emerald-700/70">{{ s.detail }}</span>
                        </span>
                    </li>
                </ol>
            </div>

            <div v-if="dressCode" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 text-center">
                <p class="font-serif text-sm uppercase tracking-[0.3em] text-emerald-600">Dress code</p>
                <p class="mt-2 text-lg text-emerald-950">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="rounded-[2rem] border border-emerald-900/10 bg-white/70 p-8 sm:p-10">
                <p class="text-center font-serif text-sm uppercase tracking-[0.3em] text-emerald-600">Good to know</p>
                <div class="mt-6 space-y-5">
                    <div v-for="(f,i) in faqs" :key="i" class="text-center">
                        <p class="font-semibold text-emerald-950">{{ f.question }}</p>
                        <p class="mt-1 text-sm leading-7 text-emerald-800/70">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-emerald-800 p-8 text-center text-white sm:p-10">
                <p class="font-serif text-2xl">Gift Registry 🌿</p>
                <p class="mx-auto mt-2 max-w-md text-sm text-emerald-100/80">{{ rsvpNote || 'Browse our wishlist and send a little something.' }}</p>
                <Link :href="registryUrl" class="mt-6 inline-flex rounded-full bg-white px-7 py-3 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50">View the registry</Link>
            </div>

            <p class="text-center font-serif text-sm italic text-emerald-700/60">With love, {{ event.user?.name }} · Powered by Gift Loft</p>
        </section>
    </div>
</template>
