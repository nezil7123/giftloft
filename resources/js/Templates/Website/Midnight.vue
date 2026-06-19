<script setup>
import { Link } from '@inertiajs/vue3';
import { useEventDisplay } from '../useEventDisplay.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull } = useEventDisplay(() => props.event);
</script>

<template>
    <div class="bg-neutral-950 text-neutral-100">
        <!-- Hero -->
        <section
            class="relative flex min-h-[88vh] items-center justify-center overflow-hidden"
            :style="event.cover_photo_url ? `background-image:url('${event.cover_photo_url}');background-size:cover;background-position:center` : ''"
        >
            <div class="absolute inset-0 bg-neutral-950/80"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(251,191,36,0.12),transparent_60%)]"></div>

            <div class="relative mx-auto max-w-3xl px-6 py-32 text-center sm:px-10">
                <p class="text-xs font-semibold uppercase tracking-[0.5em] text-amber-300/80">{{ typeLabel }}</p>
                <div class="mx-auto mt-6 h-px w-20 bg-gradient-to-r from-transparent via-amber-300/60 to-transparent"></div>
                <h1 class="mt-7 font-serif text-5xl font-bold leading-tight text-white sm:text-7xl">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="mt-5 font-serif text-xl italic text-amber-200/90">{{ tagline }}</p>
                <div class="mx-auto mt-7 h-px w-20 bg-gradient-to-r from-transparent via-amber-300/60 to-transparent"></div>
                <p v-if="event.starts_at" class="mt-7 text-base text-neutral-300">{{ fmtFull(event.starts_at) }}</p>
                <p v-if="location" class="mt-1 text-xs uppercase tracking-[0.3em] text-amber-300/60">{{ location }}</p>

                <div v-if="countdown" class="mt-10 inline-flex gap-3 sm:gap-5">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                        class="flex h-20 w-16 flex-col items-center justify-center rounded-2xl border border-amber-300/20 bg-white/5 backdrop-blur-sm sm:w-20">
                        <span class="text-3xl font-bold text-amber-200">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[10px] uppercase tracking-widest text-neutral-400">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-3xl space-y-6 px-6 py-20 sm:px-10">
            <div v-if="event.description" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8 text-center sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300/80">Our story</p>
                <p class="mt-4 whitespace-pre-line text-lg leading-8 text-neutral-300">{{ event.description }}</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div v-if="event.starts_at" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8">
                    <p class="text-sm font-semibold uppercase tracking-widest text-amber-300/70">When</p>
                    <p class="mt-2 font-semibold text-white">{{ fmtFull(event.starts_at) }}</p>
                </div>
                <div v-if="location" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8">
                    <p class="text-sm font-semibold uppercase tracking-widest text-amber-300/70">Where</p>
                    <p class="mt-2 font-semibold text-white">{{ location }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300/80">Schedule</p>
                <ol class="mt-6 space-y-5">
                    <li v-for="(s,i) in schedule" :key="i" class="flex gap-5 border-l border-amber-300/30 pl-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-300/70">{{ s.time }}</p>
                            <p class="mt-0.5 font-semibold text-white">{{ s.title }}</p>
                            <p v-if="s.detail" class="text-sm text-neutral-400">{{ s.detail }}</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div v-if="dressCode" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8 text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-300/70">Dress code</p>
                <p class="mt-2 text-lg text-white">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="rounded-3xl border border-white/10 bg-white/[0.03] p-8 sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300/80">Good to know</p>
                <div class="mt-6 space-y-5">
                    <div v-for="(f,i) in faqs" :key="i">
                        <p class="font-semibold text-white">{{ f.question }}</p>
                        <p class="mt-1 text-sm leading-7 text-neutral-400">{{ f.answer }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-amber-300/30 bg-gradient-to-br from-amber-500/10 to-transparent p-8 text-center sm:p-10">
                <p class="font-serif text-2xl text-white">Gift Registry</p>
                <p class="mx-auto mt-2 max-w-md text-sm text-neutral-300">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="mt-6 inline-flex rounded-full bg-amber-300 px-7 py-3 text-sm font-semibold text-neutral-950 transition hover:bg-amber-200">View the registry</Link>
            </div>

            <p class="text-center text-sm text-neutral-500">Hosted by {{ event.user?.name }} · Powered by Gift Loft</p>
        </section>
    </div>
</template>
