<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-gradient-to-b from-rose-50 via-amber-50 to-white">
        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-20 top-10 h-72 w-72 rounded-full bg-rose-300/40 blur-3xl"></div>
                <div class="absolute -right-20 top-40 h-72 w-72 rounded-full bg-amber-300/40 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-fuchsia-300/30 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-4xl px-6 pb-16 pt-36 text-center sm:px-10">
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold uppercase tracking-widest text-rose-500 shadow-sm ring-1 ring-rose-100">🎉 {{ typeLabel }}</span>
                <h1 class="mt-7 text-6xl font-black tracking-tight text-neutral-900 sm:text-8xl">
                    <span class="bg-gradient-to-r from-rose-500 via-fuchsia-500 to-amber-500 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                </h1>
                <p v-if="tagline" class="mt-5 text-xl font-semibold text-neutral-600">{{ tagline }}</p>

                <div v-if="event.cover_photo_url" class="mx-auto mt-10 aspect-[16/9] max-w-3xl overflow-hidden rounded-[2rem] shadow-2xl ring-4 ring-white">
                    <img :src="event.cover_photo_url" class="h-full w-full object-cover" />
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 text-sm font-semibold text-neutral-700">
                    <span v-if="event.starts_at" class="rounded-full bg-white px-4 py-2 shadow-sm">📅 {{ fmtFull(event.starts_at) }}</span>
                    <span v-if="location" class="rounded-full bg-white px-4 py-2 shadow-sm">📍 {{ location }}</span>
                </div>

                <div v-if="countdown" class="mt-8 inline-flex gap-3">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex h-20 w-16 flex-col items-center justify-center rounded-2xl bg-white shadow-lg ring-1 ring-rose-100 sm:w-20">
                        <span class="bg-gradient-to-br from-rose-500 to-amber-500 bg-clip-text text-3xl font-black text-transparent">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-4xl space-y-6 px-6 pb-20 sm:px-10">
            <div v-if="event.description" class="rounded-[2rem] bg-white p-8 shadow-lg shadow-rose-100 sm:p-10">
                <h2 class="text-2xl font-black text-neutral-900">The story 💌</h2>
                <p class="mt-4 whitespace-pre-line text-lg leading-8 text-neutral-700">{{ event.description }}</p>
            </div>

            <div v-if="schedule.length" class="rounded-[2rem] bg-white p-8 shadow-lg shadow-amber-100 sm:p-10">
                <h2 class="text-2xl font-black text-neutral-900">What's happening 🗓️</h2>
                <div class="mt-6 space-y-4">
                    <div v-for="(s,i) in schedule" :key="i" class="flex gap-4 rounded-2xl bg-gradient-to-r from-rose-50 to-amber-50 p-4">
                        <div class="shrink-0 rounded-xl bg-white px-3 py-2 text-center text-xs font-bold text-rose-500 shadow-sm">{{ s.time }}</div>
                        <div>
                            <p class="font-bold text-neutral-900">{{ s.title }}</p>
                            <p v-if="s.detail" class="text-sm text-neutral-500">{{ s.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div v-if="dressCode" class="rounded-[2rem] bg-gradient-to-br from-fuchsia-500 to-purple-600 p-8 text-white shadow-lg">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-white/70">Dress code 👗</h3>
                    <p class="mt-2 text-2xl font-black">{{ dressCode }}</p>
                </div>
                <div class="rounded-[2rem] bg-gradient-to-br from-rose-500 to-amber-500 p-8 text-white shadow-lg">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-white/70">Gifts 🎁</h3>
                    <p class="mt-2 text-sm text-white/90">{{ rsvpNote || 'Find the perfect gift on our registry.' }}</p>
                    <Link :href="registryUrl" class="mt-4 inline-flex rounded-full bg-white px-6 py-2.5 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Open registry</Link>
                </div>
            </div>

            <div v-if="faqs.length" class="rounded-[2rem] bg-white p-8 shadow-lg shadow-rose-100 sm:p-10">
                <h2 class="text-2xl font-black text-neutral-900">Questions? 🙋</h2>
                <div class="mt-6 space-y-5">
                    <div v-for="(f,i) in faqs" :key="i">
                        <p class="font-bold text-neutral-900">{{ f.question }}</p>
                        <p class="mt-1 leading-7 text-neutral-600">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <p class="pt-4 text-center text-sm font-medium text-neutral-400">Hosted by {{ event.user?.name }} · Powered by Gift Loft 🎈</p>
        </section>
    </div>
</template>
