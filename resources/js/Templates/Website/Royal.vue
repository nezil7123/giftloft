<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useEventDisplay } from '../useEventDisplay.js';
import { useReveal } from '../useReveal.js';
import { useScrollFx } from '../useScrollFx.js';

const props = defineProps({ event: { type: Object, required: true } });
const { typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, venueNote, venuePhoto, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown, fmtFull, fmtDate } = useEventDisplay(() => props.event);
useReveal();
const root = ref(null);
useScrollFx(root);

// Monogram from host initials, e.g. "Sarah & James" → "S·J"
const monogram = computed(() => {
    const names = (hosts.value || props.event.title || '').split(/\s*(?:&|and|·)\s*/i).filter(Boolean);
    const initials = names.map((n) => n.trim()[0]?.toUpperCase()).filter(Boolean).slice(0, 2);
    return initials.length > 1 ? initials.join('·') : (initials[0] ?? '✦');
});
</script>

<template>
    <div ref="root" class="bg-[#06231c] text-emerald-50">
        <!-- HERO — the crest -->
        <section class="gl-grain relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-24">
            <!-- Ornate frame -->
            <div class="pointer-events-none absolute inset-4 border border-amber-200/25 sm:inset-8"></div>
            <div class="pointer-events-none absolute inset-6 border border-amber-200/15 sm:inset-10"></div>
            <div data-fx="parallax" data-speed="0.3" class="pointer-events-none absolute -top-24 left-1/2 -ml-48 h-96 w-96 rounded-full bg-amber-300/10 blur-[90px]"></div>

            <div data-fx="hero-exit" class="relative z-10 text-center">
                <p class="gl-enter text-[11px] font-semibold uppercase tracking-[0.6em] text-amber-200/90">By royal invitation</p>

                <!-- Laurel crest draws itself -->
                <div class="relative mx-auto mt-10 h-44 w-44">
                    <svg data-fx="draw" viewBox="0 0 200 200" fill="none" class="absolute inset-0 text-amber-300/80">
                        <path d="M100 15 a85 85 0 0 1 0 170 a85 85 0 0 1 0 -170" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M38 140 q-18 -40 8 -76 M45 152 q-30 -50 2 -100" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M162 140 q18 -40 -8 -76 M155 152 q30 -50 -2 -100" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M100 178 l-7 10 M100 178 l7 10 M85 174 l-9 8 M115 174 l9 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="gl-pop font-serif text-5xl font-medium text-amber-100">{{ monogram }}</span>
                    </div>
                </div>

                <h1 data-fx="chars" class="gl-display-sm mt-10 font-serif font-medium text-amber-50">{{ hosts || event.title }}</h1>
                <p v-if="tagline" class="gl-enter gl-d2 mt-5 font-serif text-xl italic text-amber-200/90 sm:text-2xl">{{ tagline }}</p>
                <div class="gl-grow mx-auto mt-7 flex items-center justify-center gap-3">
                    <span class="h-px w-16 bg-amber-300/50"></span>
                    <span class="text-amber-300/80">❦</span>
                    <span class="h-px w-16 bg-amber-300/50"></span>
                </div>
                <p class="gl-enter gl-d3 mt-6 text-sm uppercase tracking-[0.3em] text-emerald-100/70">{{ fmtDate(event.starts_at) }}<span v-if="location"> · {{ location }}</span></p>

                <div v-if="countdown" class="gl-enter gl-d4 mt-10 inline-flex divide-x divide-amber-200/20 rounded-none border border-amber-200/30">
                    <div v-for="u in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hours'},{v:countdown.mins,l:'Minutes'},{v:countdown.secs,l:'Seconds'}]" :key="u.l"
                        class="flex w-20 flex-col items-center px-2 py-4 sm:w-24">
                        <span class="font-serif text-3xl tabular-nums text-amber-100">{{ String(u.v).padStart(2,'0') }}</span>
                        <span class="mt-1 text-[8px] uppercase tracking-[0.3em] text-emerald-100/60">{{ u.l }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- COVER -->
        <section v-if="coverPhoto" class="px-6 pb-6 sm:px-10">
            <div data-fx="img-reveal" class="mx-auto max-w-4xl overflow-hidden">
                <img :src="coverPhoto" alt="" class="aspect-[16/9] w-full object-cover" />
            </div>
        </section>

        <!-- STORY -->
        <section v-if="event.description" class="px-6 py-28 sm:px-10">
            <div class="mx-auto max-w-3xl text-center">
                <p data-reveal class="text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">Our story</p>
                <span data-reveal class="mt-6 block font-serif text-6xl leading-none text-amber-300/40">“</span>
                <p data-fx="words" class="whitespace-pre-line font-serif text-2xl font-light leading-relaxed text-emerald-50 sm:text-3xl">{{ event.description }}</p>
            </div>
        </section>

        <!-- DETAILS -->
        <section class="px-6 pb-10 sm:px-10">
            <div data-fx="batch" class="mx-auto grid max-w-5xl gap-5 sm:grid-cols-2">
                <div class="border border-amber-200/20 bg-emerald-950/40 p-8 text-center transition hover:border-amber-200/50">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-amber-200/80">The day</p>
                    <p class="mt-3 font-serif text-2xl text-amber-50">{{ fmtFull(event.starts_at) || 'To be announced' }}</p>
                </div>
                <div class="border border-amber-200/20 bg-emerald-950/40 p-8 text-center transition hover:border-amber-200/50">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-amber-200/80">The place</p>
                    <p class="mt-3 font-serif text-2xl text-amber-50">{{ location || 'To be announced' }}</p>
                </div>
            </div>

            <div v-if="schedule.length" class="mx-auto mt-5 max-w-5xl border border-amber-200/20 bg-emerald-950/40 p-8 sm:p-12">
                <p data-reveal class="text-center text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">Order of proceedings</p>
                <div class="mt-10 space-y-0">
                    <div v-for="(s,i) in schedule" :key="i" data-reveal class="grid items-baseline gap-3 border-t border-amber-200/15 py-6 sm:grid-cols-[6rem_1fr_auto]">
                        <span class="font-serif text-2xl text-amber-300/50">{{ ['I','II','III','IV','V','VI'][i] ?? i+1 }}</span>
                        <div>
                            <p class="font-serif text-2xl text-amber-50">{{ s.title }}</p>
                            <p v-if="s.detail" class="mt-1 text-sm text-emerald-100/60">{{ s.detail }}</p>
                        </div>
                        <span class="text-sm uppercase tracking-[0.2em] text-amber-200/70">{{ s.time }}</span>
                    </div>
                </div>
            </div>

            <div v-if="dressCode" data-reveal class="mx-auto mt-5 max-w-5xl border border-amber-200/20 bg-emerald-950/40 p-8 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-amber-200/80">Attire</p>
                <p class="mt-3 font-serif text-2xl text-amber-50">{{ dressCode }}</p>
            </div>

            <div v-if="faqs.length" class="mx-auto mt-5 max-w-5xl border border-amber-200/20 bg-emerald-950/40 p-8 sm:p-12">
                <p data-reveal class="text-center text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">Of note</p>
                <div class="mt-8 space-y-7">
                    <div v-for="(f,i) in faqs" :key="i" data-reveal class="text-center">
                        <p class="font-serif text-xl text-amber-50">{{ f.question }}</p>
                        <p class="mt-1.5 leading-7 text-emerald-100/70">{{ f.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VENUE -->
        <section v-if="event.venue || venueNote || venuePhoto || travel || stay" class="px-6 py-24 sm:px-10">
            <div class="mx-auto max-w-5xl text-center">
                <p data-reveal class="text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">The estate</p>
                <h2 data-fx="chars" class="mt-4 font-serif text-4xl text-amber-50 sm:text-5xl">{{ event.venue || location }}</h2>
                <p v-if="venueNote" data-fx="words" class="mx-auto mt-6 max-w-2xl font-serif text-xl font-light italic leading-8 text-amber-100/70">{{ venueNote }}</p>

                <!-- Arched frame -->
                <div v-if="venuePhoto" data-fx="scale-in" class="mx-auto mt-12 max-w-md overflow-hidden rounded-t-full border border-amber-200/30 p-3">
                    <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[3/4] w-full rounded-t-full object-cover" />
                </div>

                <div data-fx="batch" class="mt-12 grid gap-5 text-left sm:grid-cols-2">
                    <div v-if="travel" class="border border-amber-200/20 bg-emerald-950/40 p-7">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-amber-200/80">The journey</p>
                        <p class="mt-3 text-sm leading-7 text-emerald-100/80">{{ travel }}</p>
                    </div>
                    <div v-if="stay" class="border border-amber-200/20 bg-emerald-950/40 p-7">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-amber-200/80">Lodgings</p>
                        <p class="mt-3 text-sm leading-7 text-emerald-100/80">{{ stay }}</p>
                    </div>
                </div>
                <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="gl-btn mt-8 inline-flex border border-amber-200/40 px-7 py-3 text-xs font-semibold uppercase tracking-[0.25em] text-amber-100 transition hover:bg-amber-200/10">❦ Open in Maps</a>
            </div>
        </section>

        <!-- GALLERY — arched gallery hall -->
        <section v-if="gallery.length" class="px-6 pb-16 sm:px-10">
            <p data-reveal class="text-center text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">The gallery hall</p>
            <div data-fx="batch" class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-4">
                <div v-for="(p,i) in gallery" :key="i" class="gl-photo overflow-hidden rounded-t-full border border-amber-200/20 p-2">
                    <img :src="p" alt="" loading="lazy" class="aspect-[3/4] w-full rounded-t-full object-cover" />
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-28 sm:px-10">
            <div data-reveal="zoom" class="gl-grain relative mx-auto max-w-4xl border border-amber-200/30 px-8 py-20 text-center">
                <div class="pointer-events-none absolute inset-3 border border-amber-200/15"></div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.5em] text-amber-200/90">With gratitude</p>
                <h3 class="mt-5 font-serif text-4xl text-amber-50 sm:text-5xl">The wishlist</h3>
                <p class="mx-auto mt-4 max-w-md text-emerald-100/70">{{ rsvpNote || 'Browse our wishlist and send a thoughtful gift.' }}</p>
                <Link :href="registryUrl" class="gl-btn mt-9 inline-flex items-center justify-center bg-amber-200 px-10 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-950 transition hover:scale-105">View the registry</Link>
            </div>
            <p class="mt-10 text-center text-[10px] uppercase tracking-[0.3em] text-emerald-100/40">{{ event.user?.name }} · Gift Loft</p>
        </section>
    </div>
</template>
