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

const confetti = Array.from({ length: 24 }, (_, i) => ({
    left: (i * 4.3 + 2) % 100, delay: (i % 12) * 0.5, dur: 6 + (i % 6),
    color: ['bg-rose-400', 'bg-amber-400', 'bg-fuchsia-400', 'bg-sky-400', 'bg-emerald-400', 'bg-violet-400'][i % 6],
    round: i % 2 === 0,
}));
const marquee = computed(() => ['LET’S CELEBRATE', '🎉', hosts.value || props.event.title, '🎈', fmtDate(props.event.starts_at), '🥂']);
const photoStrip = computed(() => {
    const pics = [coverPhoto.value, ...gallery.value].filter(Boolean);
    return pics.length ? [...pics, ...pics, ...pics].slice(0, 12) : [];
});
</script>

<template>
    <div ref="root" class="bg-[#0e0a1f] text-white">
        <!-- HERO — aurora + holo photo card -->
        <section class="gl-grain relative flex min-h-screen items-center overflow-hidden">
            <div class="gl-mesh-dark gl-mesh-anim absolute inset-0 opacity-70"></div>
            <div class="gl-aurora"></div>
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <span v-for="(c,i) in confetti" :key="i" class="gl-fall absolute top-0 h-3 w-3" :class="[c.color, c.round ? 'rounded-full' : 'rotate-45']"
                    :style="`left:${c.left}%;animation-delay:${c.delay}s;animation-duration:${c.dur}s`"></span>
            </div>

            <div class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-14 px-6 py-28 sm:px-10 lg:grid-cols-[1.05fr_0.95fr]">
                <div data-fx="hero-exit" class="text-center lg:text-left">
                    <span class="gl-pop inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 py-2 text-xs font-bold uppercase tracking-[0.3em] backdrop-blur-md">🎉 {{ typeLabel }}</span>
                    <h1 class="mt-8 overflow-hidden">
                        <span class="gl-clip gl-display block font-black">
                            <span class="gl-pan bg-gradient-to-r from-rose-400 via-fuchsia-400 to-amber-300 bg-clip-text text-transparent">{{ hosts || event.title }}</span>
                        </span>
                    </h1>
                    <p v-if="tagline" class="gl-enter gl-d2 mt-6 text-2xl font-semibold text-white/80">{{ tagline }}</p>

                    <div class="gl-enter gl-d3 mt-9 flex flex-wrap items-center justify-center gap-3 text-sm font-semibold lg:justify-start">
                        <span v-if="event.starts_at" class="rounded-full border border-white/15 bg-white/5 px-5 py-2 backdrop-blur-md">📅 {{ fmtFull(event.starts_at) }}</span>
                        <span v-if="location" class="rounded-full border border-white/15 bg-white/5 px-5 py-2 backdrop-blur-md">📍 {{ location }}</span>
                    </div>

                    <div v-if="countdown" class="mt-10 inline-flex gap-3">
                        <div v-for="(u,idx) in [{v:countdown.days,l:'Days'},{v:countdown.hours,l:'Hrs'},{v:countdown.mins,l:'Min'},{v:countdown.secs,l:'Sec'}]" :key="u.l"
                            class="gl-float-soft gl-glow flex h-24 w-20 flex-col items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-3 backdrop-blur-md sm:w-24"
                            :style="`animation-delay:${idx*0.3}s;--gl-glow:rgba(236,72,153,0.45)`">
                            <span class="bg-gradient-to-br from-rose-300 to-amber-300 bg-clip-text text-4xl font-black tabular-nums text-transparent">{{ String(u.v).padStart(2,'0') }}</span>
                            <span class="mt-1.5 text-[10px] font-bold uppercase tracking-widest text-white/50">{{ u.l }}</span>
                        </div>
                    </div>
                </div>

                <!-- Holo photo card -->
                <div v-if="coverPhoto" class="gl-enter gl-d2 relative mx-auto w-full max-w-md">
                    <div data-fx="rotate" data-turns="0.045">
                        <div class="gl-border-glow gl-tilt rotate-2 overflow-hidden rounded-[2rem] p-1.5" style="--gl-bg:#0e0a1f">
                            <div class="gl-photo overflow-hidden rounded-[1.6rem]">
                                <img :src="coverPhoto" alt="" class="aspect-[4/5] w-full object-cover" />
                            </div>
                        </div>
                    </div>
                    <div class="gl-float absolute -left-6 -top-6 text-5xl">🎈</div>
                    <div class="gl-float absolute -bottom-5 -right-4 text-5xl" style="animation-delay:2s">🎊</div>
                </div>
            </div>
        </section>

        <!-- TEXT MARQUEE -->
        <div class="gl-pan overflow-hidden border-y border-white/10 bg-gradient-to-r from-rose-500 via-fuchsia-500 to-amber-400 py-4">
            <div class="gl-marquee gl-marquee-fast">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m,i) in marquee" :key="i" class="mx-6 text-2xl font-black uppercase tracking-tight text-white">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- PHOTO MARQUEE — scrolling film strip -->
        <div v-if="photoStrip.length" class="overflow-hidden bg-[#0e0a1f] py-8">
            <div class="gl-marquee gl-marquee-rev">
                <span v-for="g in 2" :key="g" class="flex items-center gap-5 pr-5">
                    <span v-for="(p,i) in photoStrip" :key="i" class="gl-photo block w-52 shrink-0 overflow-hidden rounded-2xl border border-white/10" :class="i % 2 ? 'rotate-1' : '-rotate-1'">
                        <img :src="p" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover" />
                    </span>
                </span>
            </div>
        </div>

        <!-- CONTENT -->
        <section class="gl-grain relative px-6 py-24 sm:px-10">
            <div class="gl-mesh-dark gl-mesh-anim absolute inset-0 opacity-40"></div>
            <div class="relative mx-auto max-w-4xl space-y-6">
                <div v-if="event.description" data-reveal class="gl-tilt rounded-[2rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:bg-white/[0.07] sm:p-10">
                    <h2 class="text-2xl font-black">The story 💌</h2>
                    <p class="mt-4 whitespace-pre-line text-lg leading-8 text-white/75">{{ event.description }}</p>
                </div>

                <div v-if="schedule.length" data-reveal class="rounded-[2rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm sm:p-10">
                    <h2 class="text-2xl font-black">What's happening 🗓️</h2>
                    <div class="mt-6 space-y-3">
                        <div v-for="(s,i) in schedule" :key="i" class="flex items-center gap-4 rounded-2xl bg-white/5 p-4 transition hover:scale-[1.02] hover:bg-white/10">
                            <div class="shrink-0 rounded-xl bg-gradient-to-br from-rose-500 to-amber-500 px-3 py-2 text-center text-xs font-black text-white">{{ s.time }}</div>
                            <div>
                                <p class="font-bold">{{ s.title }}</p>
                                <p v-if="s.detail" class="text-sm text-white/50">{{ s.detail }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="event.venue || venueNote || venuePhoto || travel || stay" data-reveal class="rounded-[2rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm sm:p-10">
                    <h2 class="text-2xl font-black">The venue 📍</h2>
                    <p class="mt-1 text-sm font-semibold text-white/40">{{ event.venue }}<span v-if="event.venue && event.location"> · </span>{{ event.location }}</p>
                    <p v-if="venueNote" class="mt-4 text-lg leading-8 text-white/75">{{ venueNote }}</p>
                    <div v-if="venuePhoto" class="mt-6 overflow-hidden rounded-[1.5rem]">
                        <img :src="venuePhoto" alt="" loading="lazy" class="aspect-[16/9] w-full object-cover" />
                    </div>
                    <div data-fx="batch" class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div v-if="travel" class="rounded-2xl bg-white/5 p-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-rose-300">🚕 Getting there</p>
                            <p class="mt-2 text-sm leading-7 text-white/65">{{ travel }}</p>
                        </div>
                        <div v-if="stay" class="rounded-2xl bg-white/5 p-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-amber-300">🛏️ Where to stay</p>
                            <p class="mt-2 text-sm leading-7 text-white/65">{{ stay }}</p>
                        </div>
                    </div>
                    <a v-if="mapUrl" :href="mapUrl" target="_blank" rel="noopener" class="gl-btn mt-6 inline-flex rounded-full bg-white px-6 py-2.5 text-sm font-bold text-fuchsia-700 transition hover:scale-105">Open in Maps 🗺️</a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div v-if="dressCode" data-reveal="left" class="gl-glow rounded-[2rem] bg-gradient-to-br from-fuchsia-500 to-purple-600 p-8 shadow-xl" style="--gl-glow:rgba(217,70,239,0.4)">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-white/70">Dress code 👗</h3>
                        <p class="mt-2 text-3xl font-black">{{ dressCode }}</p>
                    </div>
                    <div data-reveal="right" class="gl-pan rounded-[2rem] bg-gradient-to-br from-rose-500 to-amber-500 p-8 shadow-xl shadow-rose-900/40">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-white/70">Gifts 🎁</h3>
                        <p class="mt-2 text-sm text-white/90">{{ rsvpNote || 'Find the perfect gift on our wishlist.' }}</p>
                        <Link :href="registryUrl" class="gl-btn mt-4 inline-flex rounded-full bg-white px-6 py-2.5 text-sm font-bold text-rose-600 transition hover:scale-105">Open wishlist</Link>
                    </div>
                </div>

                <div v-if="faqs.length" data-reveal class="rounded-[2rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm sm:p-10">
                    <h2 class="text-2xl font-black">Questions? 🙋</h2>
                    <div class="mt-6 space-y-5">
                        <div v-for="(f,i) in faqs" :key="i">
                            <p class="font-bold">{{ f.question }}</p>
                            <p class="mt-1 leading-7 text-white/65">{{ f.answer }}</p>
                        </div>
                    </div>
                </div>

                <p class="pt-4 text-center text-sm font-medium text-white/40">Hosted by {{ event.user?.name }} · Powered by Gift Loft 🎈</p>
            </div>
        </section>
    </div>
</template>
