<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-neutral-50">
        <!-- Cover hero -->
        <section
            class="relative flex min-h-[88vh] items-end overflow-hidden bg-neutral-900"
            :style="event.cover_photo_url ? `background-image:url('${event.cover_photo_url}');background-size:cover;background-position:center` : ''"
        >
            <div v-if="!event.cover_photo_url" class="absolute inset-0 bg-gradient-to-br from-indigo-700 via-violet-700 to-purple-800"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/30"></div>
            <div class="relative mx-auto w-full max-w-5xl px-6 pb-20 pt-32 text-center sm:px-10">
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-white/70">{{ typeLabel }}</p>
                <h1 class="mt-5 font-serif text-5xl font-bold tracking-tight text-white sm:text-7xl">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="mt-4 font-serif text-xl italic text-white/85">{{ tagline }}</p>
                <p v-if="event.starts_at" class="mt-6 text-lg text-white/85">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" class="mt-2 text-sm uppercase tracking-[0.2em] text-white/65">{{ location }}</p>

                <div v-if="countdown" class="mt-10 inline-flex gap-3 sm:gap-5">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex h-20 w-16 flex-col items-center justify-center rounded-2xl border border-white/20 bg-white/10 backdrop-blur-md sm:w-20">
                        <span class="text-2xl font-bold text-white sm:text-3xl">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[10px] uppercase tracking-widest text-white/60">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20">
            <div class="mx-auto max-w-3xl space-y-6 px-6 sm:px-10">
                <div v-if="event.description" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 sm:p-10">
                    <h2 class="font-serif text-sm font-bold uppercase tracking-[0.2em] text-indigo-500">Our story</h2>
                    <p class="mt-4 whitespace-pre-line text-lg leading-8 text-neutral-700">{{ event.description }}</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div v-if="event.starts_at" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                        <h3 class="font-serif text-sm font-bold uppercase tracking-wide text-neutral-400">When</h3>
                        <p class="mt-2 font-semibold text-neutral-900">{{ fmtFull(event.starts_at) }}</p>
                    </div>
                    <div v-if="location" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                        <h3 class="font-serif text-sm font-bold uppercase tracking-wide text-neutral-400">Where</h3>
                        <p class="mt-2 font-semibold text-neutral-900">{{ location }}</p>
                    </div>
                </div>

                <!-- Schedule -->
                <div v-if="schedule.length" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 sm:p-10">
                    <h2 class="font-serif text-sm font-bold uppercase tracking-[0.2em] text-indigo-500">Schedule</h2>
                    <ol class="mt-5 space-y-5">
                        <li v-for="(s,i) in schedule" :key="i" class="flex gap-5 border-l-2 border-indigo-200 pl-5">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">{{ s.time }}</p>
                                <p class="mt-0.5 font-semibold text-neutral-900">{{ s.title }}</p>
                                <p v-if="s.detail" class="text-sm text-neutral-500">{{ s.detail }}</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div v-if="dressCode" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 text-center">
                    <h3 class="font-serif text-sm font-bold uppercase tracking-wide text-neutral-400">Dress code</h3>
                    <p class="mt-2 text-lg text-neutral-800">{{ dressCode }}</p>
                </div>

                <!-- FAQs -->
                <div v-if="faqs.length" class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70 sm:p-10">
                    <h2 class="font-serif text-sm font-bold uppercase tracking-[0.2em] text-indigo-500">Good to know</h2>
                    <div class="mt-5 space-y-5">
                        <div v-for="(f,i) in faqs" :key="i">
                            <p class="font-semibold text-neutral-900">{{ f.question }}</p>
                            <p class="mt-1 text-sm leading-7 text-neutral-600">{{ f.answer }}</p>
                        </div>
                    </div>
                </div>

                <!-- Registry CTA -->
                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-8 text-center text-white shadow-lg sm:p-10">
                    <h3 class="font-serif text-2xl font-bold">Gift Registry</h3>
                    <p v-if="rsvpNote" class="mx-auto mt-2 max-w-md text-sm text-indigo-100">{{ rsvpNote }}</p>
                    <p v-else class="mx-auto mt-2 max-w-md text-sm text-indigo-100">Browse our wishlist and send a thoughtful gift.</p>
                    <Link :href="registryUrl" class="mt-6 inline-flex items-center justify-center rounded-full bg-white px-7 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">View the registry</Link>
                </div>

                <p class="text-center text-sm text-neutral-400">Hosted by {{ event.user?.name }} · Powered by Gift Loft</p>
            </div>
        </section>
    </div>
</template>
