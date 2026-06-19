<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-amber-50/60">
        <!-- Hero -->
        <section class="mx-auto max-w-4xl px-6 pb-12 pt-32 text-center sm:px-10">
            <span class="inline-block -rotate-2 rounded-md bg-amber-200/70 px-3 py-1 text-xs font-bold uppercase tracking-widest text-amber-800">{{ typeLabel }}</span>
            <h1 class="mt-6 font-serif text-5xl font-black leading-tight text-neutral-900 sm:text-7xl">{{ hosts || event.title }}</h1>
            <p v-if="tagline" class="mt-4 text-xl text-neutral-500">{{ tagline }}</p>

            <!-- taped photo -->
            <div v-if="event.cover_photo_url" class="relative mx-auto mt-10 w-full max-w-2xl rotate-[-1.5deg]">
                <div class="absolute -top-3 left-1/2 h-6 w-24 -translate-x-1/2 rotate-2 bg-amber-200/60"></div>
                <div class="overflow-hidden rounded-sm border-8 border-white bg-white shadow-xl">
                    <img :src="event.cover_photo_url" class="aspect-[4/3] w-full object-cover" />
                </div>
            </div>

            <div class="mt-9 flex flex-wrap items-center justify-center gap-3 text-sm font-medium text-neutral-700">
                <span v-if="event.starts_at" class="rotate-1 rounded-md bg-white px-4 py-2 shadow-sm ring-1 ring-neutral-200">📅 {{ fmtFull(event.starts_at) }}</span>
                <span v-if="location" class="-rotate-1 rounded-md bg-white px-4 py-2 shadow-sm ring-1 ring-neutral-200">📍 {{ location }}</span>
            </div>

            <div v-if="countdown" class="mt-8 inline-flex gap-3">
                <div v-for="(u,idx) in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'}]" :key="u.l"
                    class="flex h-20 w-16 flex-col items-center justify-center rounded-lg border-4 border-white bg-rose-100 shadow-md sm:w-20"
                    :class="idx % 2 ? 'rotate-2' : '-rotate-2'">
                    <span class="text-3xl font-black text-rose-500">{{ String(u.v).padStart(2,'0') }}</span>
                    <span class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ u.l }}</span>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-3xl space-y-8 px-6 pb-20 sm:px-10">
            <div v-if="event.description" class="rotate-[0.4deg] rounded-xl border-8 border-white bg-white p-8 shadow-lg sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">Our story</p>
                <p class="mt-4 whitespace-pre-line text-lg leading-8 text-neutral-700">{{ event.description }}</p>
            </div>

            <div v-if="schedule.length" class="-rotate-[0.4deg] rounded-xl border-8 border-white bg-white p-8 shadow-lg sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">The day</p>
                <ol class="mt-6 space-y-4">
                    <li v-for="(s,i) in schedule" :key="i" class="flex gap-4">
                        <span class="shrink-0 -rotate-2 rounded-md bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ s.time }}</span>
                        <div>
                            <p class="font-bold text-neutral-900">{{ s.title }}</p>
                            <p v-if="s.detail" class="text-sm text-neutral-500">{{ s.detail }}</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div v-if="dressCode" class="mx-auto w-fit rotate-1 rounded-md bg-white px-6 py-4 text-center shadow-md ring-1 ring-neutral-200">
                <p class="text-xs uppercase tracking-widest text-neutral-400">Dress code</p>
                <p class="mt-1 font-serif text-lg text-neutral-800">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="rotate-[0.4deg] rounded-xl border-8 border-white bg-white p-8 shadow-lg sm:p-10">
                <p class="font-serif text-sm uppercase tracking-[0.25em] text-rose-400">Questions</p>
                <div class="mt-5 space-y-4">
                    <div v-for="(f,i) in faqs" :key="i">
                        <p class="font-bold text-neutral-900">{{ f.question }}</p>
                        <p class="mt-1 leading-7 text-neutral-600">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-rose-500 p-8 text-center text-white shadow-lg sm:p-10">
                <p class="font-serif text-2xl">Our Wishlist 🎁</p>
                <p class="mx-auto mt-2 max-w-md text-sm text-rose-100">{{ rsvpNote || 'A few things that would make us smile.' }}</p>
                <Link :href="registryUrl" class="mt-6 inline-flex rounded-full bg-white px-7 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">Open the registry</Link>
            </div>

            <p class="text-center font-serif text-sm italic text-neutral-400">Made with love by {{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
