<script setup>
import { Link } from '@inertiajs/vue3';
import Seo from '@/Components/Seo.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({ canLogin: Boolean, canRegister: Boolean });

// ── Marquee band ──
const marqueeItems = ['Weddings', '✦', 'Birthdays', '✦', 'Baby Showers', '✦', 'Anniversaries', '✦', 'Proposals', '✦', 'Graduations', '✦', 'Housewarmings', '✦'];

// ── Hero starfield (deterministic positions) ──
const stars = Array.from({ length: 24 }, (_, i) => ({
    top: (i * 41) % 100, left: (i * 61) % 100, delay: (i % 8) * 0.35, size: i % 5 === 0 ? 2 : 1,
}));

// ── Celebration categories (full Tailwind class strings so JIT picks them up) ──
// Styled to match the pastel feature cards above: white-core gradients,
// soft rings, tinted glows, dark text.
const categories = [
    { name: 'Wedding',     emoji: '💍', blurb: 'Celebrate your love story with a site your guests will adore.', bg: 'from-rose-50 via-white to-white',    ring: 'ring-rose-100/80',    glow: 'shadow-[0_24px_60px_-24px_rgba(244,63,94,0.35)]',   blob: 'bg-rose-200/40',    tile: 'bg-rose-100 ring-rose-200/70',       accent: 'text-rose-500' },
    { name: 'Birthday',    emoji: '🎂', blurb: 'Make every birthday legendary with your own party page.',       bg: 'from-amber-50 via-white to-white',   ring: 'ring-amber-100/80',   glow: 'shadow-[0_24px_60px_-24px_rgba(245,158,11,0.35)]',  blob: 'bg-amber-200/40',   tile: 'bg-amber-100 ring-amber-200/70',     accent: 'text-amber-500' },
    { name: 'Engagement',  emoji: '💞', blurb: 'Announce the big yes and let everyone share the joy.',           bg: 'from-fuchsia-50 via-white to-white', ring: 'ring-fuchsia-100/80', glow: 'shadow-[0_24px_60px_-24px_rgba(217,70,239,0.35)]',  blob: 'bg-fuchsia-200/40', tile: 'bg-fuchsia-100 ring-fuchsia-200/70', accent: 'text-fuchsia-500' },
    { name: 'Baby Shower', emoji: '👶', blurb: 'Welcome your little one with a page as sweet as they are.',         bg: 'from-sky-50 via-white to-white',     ring: 'ring-sky-100/80',     glow: 'shadow-[0_24px_60px_-24px_rgba(14,165,233,0.35)]',  blob: 'bg-sky-200/40',     tile: 'bg-sky-100 ring-sky-200/70',         accent: 'text-sky-500' },
    { name: 'Anniversary', emoji: '💕', blurb: 'Mark the years together with a page full of memories.',          bg: 'from-red-50 via-white to-white',     ring: 'ring-red-100/80',     glow: 'shadow-[0_24px_60px_-24px_rgba(239,68,68,0.35)]',   blob: 'bg-red-200/40',     tile: 'bg-red-100 ring-red-200/70',         accent: 'text-red-500' },
    { name: 'Proposal',    emoji: '💜', blurb: 'Plan the perfect moment and share it beautifully.',              bg: 'from-violet-50 via-white to-white',  ring: 'ring-violet-100/80',  glow: 'shadow-[0_24px_60px_-24px_rgba(139,92,246,0.35)]',  blob: 'bg-violet-200/40',  tile: 'bg-violet-100 ring-violet-200/70',   accent: 'text-violet-500' },
    { name: 'Graduation',  emoji: '🎓', blurb: 'Celebrate the milestone and gather everyone to cheer.',          bg: 'from-indigo-50 via-white to-white',  ring: 'ring-indigo-100/80',  glow: 'shadow-[0_24px_60px_-24px_rgba(99,102,241,0.35)]',  blob: 'bg-indigo-200/40',  tile: 'bg-indigo-100 ring-indigo-200/70',   accent: 'text-indigo-500' },
    { name: 'Housewarming',emoji: '🏡', blurb: 'Open the doors to your new home with a warm invite.',            bg: 'from-emerald-50 via-white to-white', ring: 'ring-emerald-100/80', glow: 'shadow-[0_24px_60px_-24px_rgba(16,185,129,0.35)]',  blob: 'bg-emerald-200/40', tile: 'bg-emerald-100 ring-emerald-200/70', accent: 'text-emerald-500' },
];

// ── Scroll reveal ──
let observer;

// ── Parallax collage ──
const parallaxSection = ref(null);
const trackA = ref(null);
const trackB = ref(null);
let ticking = false;

function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
        const el = parallaxSection.value;
        if (el) {
            const rect = el.getBoundingClientRect();
            const vh = window.innerHeight || document.documentElement.clientHeight;
            // p: -0.5 (section below) → 0.5 (section above), 0 ≈ centered
            const p = (vh - rect.top) / (vh + rect.height) - 0.5;
            if (trackA.value) trackA.value.style.transform = `translate3d(0, ${p * -90}px, 0)`;
            if (trackB.value) trackB.value.style.transform = `translate3d(0, ${p * 70}px, 0)`;
        }
        ticking = false;
    });
}

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => entries.forEach((e) => {
            if (e.isIntersecting) { e.target.classList.add('is-visible'); observer.unobserve(e.target); }
        }),
        { threshold: 0.08 }
    );
    document.querySelectorAll('[data-animate]').forEach((el) => observer.observe(el));

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    observer?.disconnect();
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);
});
</script>

<template>
    <PublicLayout hero-brand>
        <Seo
            title="ComeYay — Free Event Websites &amp; Digital Invitations"
            description="Create a free event website and digital invitations for your wedding, birthday, baby shower or party. 22 premium designs, schedule, travel details and photo gallery — live in minutes."
        />

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  HERO — cinematic mesh + grain           ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="gl-grain relative flex h-[100svh] items-center overflow-hidden bg-[#070b16] pt-[var(--nav-offset,62px)]">
            <!-- Animated mesh + blobs — deep midnight navy so the logo's coral/orange/purple carry the color -->
            <div class="pointer-events-none absolute inset-0">
                <div class="gl-mesh-midnight gl-mesh-anim absolute inset-0 opacity-90"></div>
                <div class="gl-blob absolute -left-48 top-0 h-[600px] w-[600px] rounded-full bg-indigo-900/30 blur-3xl"></div>
                <div class="gl-blob absolute -right-32 bottom-0 h-[500px] w-[500px] rounded-full bg-blue-900/25 blur-3xl" style="animation-delay:5s"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#070b16]"></div>
            </div>

            <!-- Starfield -->
            <div class="pointer-events-none absolute inset-0">
                <span v-for="(s, i) in stars" :key="i" class="gl-twinkle absolute rounded-full bg-white"
                    :style="`top:${s.top}%;left:${s.left}%;height:${s.size}px;width:${s.size}px;animation-delay:${s.delay}s`"></span>
            </div>

            <!-- Flanking glass mockups (xl+) — sit behind the centered copy -->
            <div class="pointer-events-none absolute inset-0 hidden xl:block" aria-hidden="true">
                <!-- Left: live event card -->
                <div class="gl-float-soft gl-pop absolute left-10 top-[24%] w-[270px] -rotate-6 overflow-hidden rounded-3xl border border-white/15 bg-white/5 shadow-2xl backdrop-blur-xl 2xl:left-24">
                    <div class="bg-gradient-to-r from-rose-600/40 to-pink-600/30 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">💍</span>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-white/50">Wedding</p>
                                <p class="text-sm font-semibold text-white">Sarah & James</p>
                            </div>
                            <span class="gl-pulse ml-auto rounded-full bg-emerald-500/30 px-2.5 py-1 text-[10px] font-semibold text-emerald-300">Live</span>
                        </div>
                    </div>
                    <div class="space-y-3 px-5 py-4">
                        <div class="flex gap-2 text-xs text-white/60">
                            <span>📅 March 15</span><span>·</span><span>Mumbai</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[10px] text-white/65">Schedule</span>
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[10px] text-white/65">Travel &amp; stay</span>
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[10px] text-white/65">FAQs</span>
                        </div>
                    </div>
                </div>

                <!-- Left: share-link chip -->
                <div class="gl-float gl-pop gl-d3 absolute left-24 top-[58%] -rotate-3 rounded-2xl border border-violet-400/20 bg-violet-600/20 px-4 py-2.5 backdrop-blur-md 2xl:left-40" style="animation-delay:1.2s,.45s">
                    <p class="text-xs text-violet-200">🔗 comeyay.app/e/sarah-james</p>
                </div>

                <!-- Right: schedule card -->
                <div class="gl-float-soft gl-pop gl-d2 absolute right-10 top-[30%] w-[250px] rotate-6 rounded-3xl border border-white/15 bg-white/5 p-5 shadow-2xl backdrop-blur-xl 2xl:right-24" style="animation-delay:1.8s,.3s">
                    <p class="text-[10px] uppercase tracking-widest text-white/45">The day</p>
                    <div class="mt-3 space-y-3">
                        <div class="flex items-baseline gap-3">
                            <span class="w-14 shrink-0 text-[10px] tabular-nums text-white/40">4:00 PM</span>
                            <p class="text-xs text-white/70">Mehndi by the garden</p>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="w-14 shrink-0 text-[10px] tabular-nums text-white/40">6:30 PM</span>
                            <p class="text-xs text-white/70">Ceremony &amp; vows</p>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="w-14 shrink-0 text-[10px] tabular-nums text-white/40">8:00 PM</span>
                            <p class="text-xs text-white/70">Dinner &amp; dancing</p>
                        </div>
                    </div>
                </div>

                <!-- Right: countdown toast -->
                <div class="gl-float-soft gl-pop gl-d4 absolute right-20 top-[62%] rotate-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 shadow-xl shadow-emerald-900/40 2xl:right-36" style="animation-delay:.8s,.6s">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🎉</span>
                        <div>
                            <p class="text-sm font-semibold text-white">12 days to go!</p>
                            <p class="text-xs text-emerald-200">Live countdown on your page</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Centered copy — minimal, airy -->
            <div class="relative mx-auto flex w-full max-w-2xl flex-col items-center px-6 py-8 text-center sm:px-10">

                <!-- Brand moment: big logo + tagline -->
                <img src="/brand/comeyay-logo-white.png?v=2" alt="ComeYay" class="gl-enter h-14 w-auto sm:h-20 lg:h-24" />
                <p class="gl-enter gl-d1 mt-5 text-[10px] font-semibold uppercase tracking-[0.55em] text-white/40 sm:text-[11px]">
                    Invite <span class="mx-1.5 text-rose-400">•</span> Plan <span class="mx-1.5 text-amber-400">•</span> Celebrate
                </p>

                <h1 class="mt-12 text-[clamp(1.7rem,3.2vw,2.6rem)] font-bold leading-[1.15] tracking-[-0.02em] text-white/95 sm:mt-14">
                    <span class="gl-clip block">Every celebration</span>
                    <span class="gl-clip block" style="animation-delay:.14s">deserves a
                        <span class="gl-pan bg-gradient-to-r from-rose-400 via-amber-300 to-violet-400 bg-clip-text font-extrabold text-transparent">Yay!</span>
                    </span>
                </h1>

                <p class="gl-enter gl-d2 mt-4 text-sm leading-6 text-white/45 sm:text-base sm:leading-7">
                    Beautiful event websites &amp; invitations — live in minutes.
                </p>

                <div class="gl-enter gl-d3 mt-10 flex w-full flex-col gap-3 sm:mt-11 sm:w-auto sm:flex-row sm:items-center">
                    <Link href="/register" class="gl-btn inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-bold text-neutral-900 shadow-xl shadow-black/30 transition hover:scale-105">
                        Get started free
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <Link href="/templates" class="inline-flex items-center justify-center rounded-full border border-white/20 px-8 py-3 text-sm font-semibold text-white/70 backdrop-blur-sm transition hover:border-white/50 hover:text-white">
                        See designs
                    </Link>
                </div>

                <!-- Social proof — one quiet line -->
                <div class="gl-enter gl-d4 mt-10 hidden items-center gap-3 sm:flex">
                    <div class="flex -space-x-2">
                        <div class="h-6 w-6 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-rose-400 to-pink-600"></div>
                        <div class="h-6 w-6 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-violet-400 to-purple-600"></div>
                        <div class="h-6 w-6 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-amber-400 to-orange-500"></div>
                    </div>
                    <p class="text-xs text-white/40"><span class="font-semibold text-white/70">10,000+</span> celebrations</p>
                </div>
            </div>

            <!-- Scroll hint — anchored to the section, well clear of the copy -->
            <div class="absolute bottom-5 left-1/2 hidden -translate-x-1/2 flex-col items-center gap-1.5 lg:flex">
                <p class="text-[10px] uppercase tracking-[0.2em] text-white/25">Scroll</p>
                <div class="gl-float-soft text-white/25">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  MARQUEE BAND                            ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <div class="overflow-hidden border-y border-white/10 bg-neutral-950 py-5">
            <div class="gl-marquee">
                <span v-for="g in 2" :key="g" class="flex items-center">
                    <span v-for="(m, i) in marqueeItems" :key="i"
                        class="mx-6 font-black uppercase tracking-tight"
                        :class="m === '✦' ? 'text-xl text-violet-400/70' : (i % 4 === 0 ? 'gl-outline text-3xl text-white' : 'text-3xl text-white')">{{ m }}</span>
                </span>
            </div>
        </div>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  FEATURES — parallax showcase            ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section ref="parallaxSection" class="overflow-hidden bg-gradient-to-b from-white to-neutral-50 py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="grid items-center gap-16 lg:grid-cols-2 lg:gap-20">

                    <!-- LEFT · intro -->
                    <div data-animate class="lg:sticky lg:top-28">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">Everything you need</p>
                        <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-6xl">One platform,<br/>endless<br/><span class="font-serif font-medium italic text-indigo-600">celebration.</span></h2>
                        <p class="mt-6 max-w-md text-lg leading-8 text-neutral-500">Beautiful, free, and uniquely yours — everything your guests need to know, gathered on one page they'll actually enjoy opening.</p>

                        <div class="mt-8 flex flex-wrap gap-2.5">
                            <span class="rounded-full bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-600 ring-1 ring-rose-100">Event websites</span>
                            <span class="rounded-full bg-violet-50 px-3.5 py-1.5 text-xs font-semibold text-violet-600 ring-1 ring-violet-100">Invitations</span>
                            <span class="rounded-full bg-amber-50 px-3.5 py-1.5 text-xs font-semibold text-amber-600 ring-1 ring-amber-100">22 designs</span>
                            <span class="rounded-full bg-indigo-50 px-3.5 py-1.5 text-xs font-semibold text-indigo-600 ring-1 ring-indigo-100">Schedule</span>
                            <span class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-semibold text-emerald-600 ring-1 ring-emerald-100">Travel &amp; stay</span>
                            <span class="rounded-full bg-fuchsia-50 px-3.5 py-1.5 text-xs font-semibold text-fuchsia-600 ring-1 ring-fuchsia-100">Photo gallery</span>
                        </div>

                        <Link href="/register" class="gl-btn mt-10 inline-flex items-center gap-2 rounded-full bg-neutral-950 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-neutral-800">
                            Start for free
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </Link>
                    </div>

                    <!-- RIGHT · parallax feature-card collage -->
                    <div class="relative grid grid-cols-2 gap-4 sm:gap-5">

                        <!-- Track A (drifts up) -->
                        <div ref="trackA" class="space-y-4 will-change-transform sm:space-y-5">

                            <!-- 1 · Event Website (rose) -->
                            <div data-animate class="pcard pcard-up" style="transition-delay:.05s">
                                <div class="group rotate-[-2deg] overflow-hidden rounded-3xl bg-gradient-to-br from-rose-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(244,63,94,0.35)] ring-1 ring-rose-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-rose-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-500 ring-1 ring-rose-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-rose-500">Event Website</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Your Moment, Your Page</h3>
                                </div>
                            </div>

                            <!-- 3 · 22 Premium Designs (amber) -->
                            <div data-animate class="pcard pcard-up" style="transition-delay:.18s">
                                <div class="group rotate-[1.5deg] overflow-hidden rounded-3xl bg-gradient-to-br from-amber-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(245,158,11,0.35)] ring-1 ring-amber-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-500 ring-1 ring-amber-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-amber-500">22 Premium Designs</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">A Look for Every Love</h3>
                                </div>
                            </div>

                            <!-- 5 · Photos & Countdown (fuchsia) -->
                            <div data-animate class="pcard pcard-up" style="transition-delay:.3s">
                                <div class="group rotate-[-1deg] overflow-hidden rounded-3xl bg-gradient-to-br from-fuchsia-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(217,70,239,0.35)] ring-1 ring-fuchsia-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-fuchsia-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-fuchsia-100 text-fuchsia-500 ring-1 ring-fuchsia-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-fuchsia-500">Photos &amp; Countdown</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Build the Excitement</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Track B (drifts down, offset start) -->
                        <div ref="trackB" class="space-y-4 pt-10 will-change-transform sm:space-y-5 sm:pt-16">

                            <!-- 2 · Digital Invitations (indigo) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.1s">
                                <div class="group rotate-[2deg] overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(99,102,241,0.35)] ring-1 ring-indigo-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-500 ring-1 ring-indigo-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12M3.75 7.5h16.5a1.5 1.5 0 011.5 1.5v.75H2.25V9a1.5 1.5 0 011.5-1.5zM12 7.5v12" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-500">Digital Invitations</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Invites Worth Sharing</h3>
                                </div>
                            </div>

                            <!-- 4 · Schedule & FAQs (violet) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.22s">
                                <div class="group rotate-[-1.5deg] overflow-hidden rounded-3xl bg-gradient-to-br from-violet-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(139,92,246,0.35)] ring-1 ring-violet-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-violet-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-500 ring-1 ring-violet-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-violet-500">Schedule &amp; FAQs</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Every Detail, Answered</h3>
                                </div>
                            </div>

                            <!-- 6 · Travel & Stay (emerald) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.34s">
                                <div class="group rotate-[1deg] overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(16,185,129,0.35)] ring-1 ring-emerald-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-emerald-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-500 ring-1 ring-emerald-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-500">Travel &amp; Stay</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Get Everyone There</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  CATEGORIES                              ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="bg-gradient-to-b from-neutral-50 via-white to-white py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">Made for every moment</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-6xl">Every celebration,<br/><span class="font-serif font-medium italic text-indigo-600">beautifully</span> done.</h2>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="(cat, i) in categories"
                        :key="cat.name"
                        href="/register"
                        data-animate
                        :style="`transition-delay:${i * 0.06}s`"
                        class="cat-card group relative block cursor-pointer overflow-hidden rounded-3xl bg-gradient-to-br p-7 ring-1 transition-all duration-500 hover:-translate-y-2 hover:rotate-0"
                        :class="[cat.bg, cat.ring, cat.glow, i % 2 === 0 ? 'rotate-[-0.75deg]' : 'rotate-[0.75deg]']"
                    >
                        <!-- soft corner glow -->
                        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full blur-2xl transition-transform duration-700 group-hover:scale-150" :class="cat.blob"></div>
                        <!-- shine sweep -->
                        <div class="pointer-events-none absolute inset-0 -translate-x-[120%] -skew-x-12 bg-gradient-to-r from-transparent via-white/60 to-transparent transition-transform duration-[900ms] ease-out group-hover:translate-x-[120%]"></div>

                        <!-- floating emoji tile -->
                        <div class="cat-emoji relative flex h-14 w-14 items-center justify-center rounded-2xl text-3xl ring-1 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6" :class="cat.tile" :style="`animation-delay:${i * 0.35}s`">
                            {{ cat.emoji }}
                        </div>

                        <h3 class="relative mt-5 text-xl font-bold tracking-tight text-neutral-900">{{ cat.name }}</h3>
                        <p class="relative mt-2 text-sm leading-6 text-neutral-500">{{ cat.blurb }}</p>
                        <span class="relative mt-5 inline-flex items-center gap-1.5 text-xs font-bold" :class="cat.accent">
                            Get started
                            <svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  TEMPLATES SHOWCASE                      ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="border-t border-neutral-200 bg-neutral-50 py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">Designs that wow</p>
                        <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-6xl">Twenty-three designs,<br/>endlessly <span class="font-serif font-medium italic text-indigo-600">yours.</span></h2>
                    </div>
                    <Link href="/templates" class="group inline-flex shrink-0 items-center gap-2 text-sm font-bold text-neutral-900 transition hover:text-indigo-600">
                        Explore the gallery
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </Link>
                </div>

                <div class="mt-14 grid gap-6 sm:grid-cols-3">
                    <!-- Midnight -->
                    <Link href="/templates" data-animate class="group relative block rotate-[-1.5deg] overflow-hidden rounded-[1.75rem] bg-[#080611] p-8 shadow-lg transition-all duration-500 hover:rotate-0 hover:-translate-y-2 hover:shadow-2xl" style="transition-delay:.05s">
                        <span v-for="i in 6" :key="i" class="gl-twinkle absolute h-1 w-1 rounded-full bg-amber-100"
                            :style="`top:${[12,30,18,64,42,78][i-1]}%;left:${[15,80,50,25,70,88][i-1]}%;animation-delay:${i*0.4}s`"></span>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-amber-300/80">The Midnight</p>
                        <p class="mt-8 bg-gradient-to-b from-amber-100 to-amber-300 bg-clip-text font-serif text-4xl text-transparent">Sarah &amp; James</p>
                        <p class="mt-2 font-serif text-sm italic text-white/40">An evening to remember</p>
                        <div class="mt-10 h-px w-16 bg-amber-300/40 transition-all duration-500 group-hover:w-28"></div>
                        <p class="mt-4 text-[10px] uppercase tracking-widest text-white/30">Event website</p>
                    </Link>

                    <!-- Botanical -->
                    <Link href="/templates" data-animate class="group relative block overflow-hidden rounded-[1.75rem] bg-[#f3f4ee] p-8 shadow-lg ring-1 ring-emerald-900/10 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl sm:translate-y-6" style="transition-delay:.15s">
                        <div class="gl-float pointer-events-none absolute -right-4 -top-4 text-6xl opacity-15">🌿</div>
                        <div class="gl-float pointer-events-none absolute -bottom-5 -left-3 text-5xl opacity-15" style="animation-delay:2s">🍃</div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-emerald-700">The Botanical</p>
                        <p class="mt-8 font-serif text-4xl text-emerald-950">Meera &amp; Dev</p>
                        <p class="mt-2 font-serif text-sm italic text-emerald-700/70">Growing together, always</p>
                        <div class="mt-10 flex items-center gap-2 text-emerald-600/60">
                            <span class="h-px w-10 bg-emerald-600/40 transition-all duration-500 group-hover:w-20"></span>❀
                        </div>
                        <p class="mt-4 text-[10px] uppercase tracking-widest text-emerald-800/40">Event website</p>
                    </Link>

                    <!-- Modern -->
                    <Link href="/templates" data-animate class="group relative block rotate-[1.5deg] overflow-hidden rounded-[1.75rem] bg-[#f4f3f0] p-8 shadow-lg ring-1 ring-neutral-900/10 transition-all duration-500 hover:rotate-0 hover:-translate-y-2 hover:shadow-2xl" style="transition-delay:.25s">
                        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">The Modern</p>
                        <p class="mt-8 text-4xl font-black uppercase leading-[0.95] tracking-tight text-neutral-950">Aisha<br/>&amp; Rahul</p>
                        <p class="mt-3 text-sm text-neutral-500">12 · 04 · 2026 — Goa</p>
                        <div class="mt-10 h-0.5 w-16 bg-neutral-950 transition-all duration-500 group-hover:w-28"></div>
                        <p class="mt-4 text-[10px] uppercase tracking-widest text-neutral-400">Event website</p>
                    </Link>
                </div>

                <p data-animate class="mt-10 text-center text-sm text-neutral-400">22 website designs · 21 invitation cards · every event type, beautifully themed</p>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  HOW IT WORKS (dark)                     ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="gl-grain relative overflow-hidden bg-neutral-950 py-28">
            <div class="gl-mesh-dark gl-mesh-anim pointer-events-none absolute inset-0 opacity-30"></div>
            <div class="relative mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-violet-400">Create · Design · Share · Celebrate</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-6xl">How ComeYay works.</h2>
                    <p class="mt-5 text-sm leading-7 text-white/50">One link does it all — your invitation, your event website, and every detail your guests need.</p>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-white/20 hover:bg-white/[0.06]" style="transition-delay:.05s">
                        <p class="bg-gradient-to-br from-violet-400 to-purple-600 bg-clip-text text-7xl font-black leading-none text-transparent">01</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Create your event</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Weddings, birthdays, baby showers, proposals — set the date, venue, and cover photo in minutes.</p>
                        <ul class="mt-4 space-y-1.5 text-xs leading-5 text-white/40">
                            <li><span class="text-violet-400">✓</span> 8 event types, all beautifully themed</li>
                            <li><span class="text-violet-400">✓</span> Venue photo, map link & travel directions</li>
                            <li><span class="text-violet-400">✓</span> Live countdown for your guests</li>
                        </ul>
                        <div class="gl-float absolute right-6 top-6 text-4xl opacity-10 transition group-hover:opacity-25">🎉</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-violet-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>

                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-white/20 hover:bg-white/[0.06]" style="transition-delay:.1s">
                        <p class="bg-gradient-to-br from-fuchsia-400 to-pink-600 bg-clip-text text-7xl font-black leading-none text-transparent">02</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Design website & invitation</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Pick from 22 animated website designs and 21 invitation cards, then make them yours — no code, ever.</p>
                        <ul class="mt-4 space-y-1.5 text-xs leading-5 text-white/40">
                            <li><span class="text-fuchsia-400">✓</span> Your story, schedule, FAQs & photo gallery</li>
                            <li><span class="text-fuchsia-400">✓</span> Shareable invitation card for WhatsApp & email</li>
                            <li><span class="text-fuchsia-400">✓</span> Cinematic scroll animations built in</li>
                        </ul>
                        <div class="gl-float absolute right-6 top-6 text-4xl opacity-10 transition group-hover:opacity-25" style="animation-delay:1s">💌</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-fuchsia-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>

                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-white/20 hover:bg-white/[0.06]" style="transition-delay:.15s">
                        <p class="bg-gradient-to-br from-rose-400 to-pink-600 bg-clip-text text-7xl font-black leading-none text-transparent">03</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Add every detail</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">The schedule, the dress code, how to get there, where to stay — all the questions guests would otherwise text you.</p>
                        <ul class="mt-4 space-y-1.5 text-xs leading-5 text-white/40">
                            <li><span class="text-rose-400">✓</span> Hour-by-hour schedule for every function</li>
                            <li><span class="text-rose-400">✓</span> Travel directions, hotels & a map link</li>
                            <li><span class="text-rose-400">✓</span> Dress code, FAQs & a note from the hosts</li>
                        </ul>
                        <div class="gl-float absolute right-6 top-6 text-4xl opacity-10 transition group-hover:opacity-25" style="animation-delay:2s">📍</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-rose-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>

                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm transition hover:border-white/20 hover:bg-white/[0.06]" style="transition-delay:.2s">
                        <p class="bg-gradient-to-br from-amber-400 to-orange-500 bg-clip-text text-7xl font-black leading-none text-transparent">04</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Share & celebrate</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Send one link on WhatsApp, or save your invitation as an image and post it anywhere. Guests just tap and see everything.</p>
                        <ul class="mt-4 space-y-1.5 text-xs leading-5 text-white/40">
                            <li><span class="text-amber-400">✓</span> One short link to your whole event</li>
                            <li><span class="text-amber-400">✓</span> Download your invitation as a JPG to share</li>
                            <li><span class="text-amber-400">✓</span> Keep it public or private — your call</li>
                        </ul>
                        <div class="gl-float absolute right-6 top-6 text-4xl opacity-10 transition group-hover:opacity-25" style="animation-delay:3s">✨</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-amber-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  TESTIMONIALS                            ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="bg-white py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">Loved by real people</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-6xl">Real stories,<br/>real <span class="font-serif font-medium italic text-indigo-600">love.</span></h2>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-3">
                    <div data-animate class="rounded-[1.75rem] bg-neutral-50 p-8 ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:bg-white hover:shadow-xl" style="transition-delay:.04s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 font-serif text-lg italic leading-8 text-neutral-700">"Our guests couldn't stop complimenting our event page. Sharing one link meant nobody had to ask us for directions or timings."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-rose-400 to-pink-600 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-950">Priya & Arjun</p>
                                <p class="text-xs text-neutral-400">Wedding · Mumbai, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div data-animate class="rounded-[1.75rem] bg-neutral-50 p-8 ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:bg-white hover:shadow-xl" style="transition-delay:.08s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 font-serif text-lg italic leading-8 text-neutral-700">"Setting up our baby shower page took 15 minutes. The design looked so professional and our family found it super easy to use."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-950">Sneha & Rohan</p>
                                <p class="text-xs text-neutral-400">Baby Shower · Bangalore, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div data-animate class="rounded-[1.75rem] bg-neutral-50 p-8 ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:bg-white hover:shadow-xl" style="transition-delay:.12s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 font-serif text-lg italic leading-8 text-neutral-700">"The proposal page I built on ComeYay was stunning. She said yes and immediately wanted to show everyone the site!"</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-violet-400 to-purple-600 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-950">Kabir Mehta</p>
                                <p class="text-xs text-neutral-400">Proposal · Delhi, 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  FINAL CTA — luxe mesh container         ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="bg-white px-6 pb-28 pt-4 sm:px-10">
            <div data-animate class="gl-grain relative mx-auto max-w-6xl overflow-hidden rounded-[3rem] shadow-2xl">
                <div class="gl-mesh-dark gl-mesh-anim absolute inset-0"></div>
                <div class="pointer-events-none absolute inset-0 opacity-[0.06]" style="background-image:radial-gradient(white 1px,transparent 1px);background-size:28px 28px"></div>
                <span v-for="i in 8" :key="i" class="gl-twinkle absolute h-1 w-1 rounded-full bg-white"
                    :style="`top:${[15,25,70,45,80,20,60,35][i-1]}%;left:${[10,85,20,60,75,40,90,30][i-1]}%;animation-delay:${i*0.3}s`"></span>

                <div class="relative px-8 py-24 text-center sm:py-28">
                    <div class="gl-float-soft mb-6 inline-block text-6xl">🎊</div>
                    <h2 class="gl-display-sm font-extrabold text-white">Your celebration<br/>starts here.</h2>
                    <p class="mx-auto mt-6 max-w-lg text-lg leading-8 text-white/60">Join thousands of couples and families who've made their special moments unforgettable with ComeYay.</p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link href="/register" class="gl-btn inline-flex items-center justify-center gap-2 rounded-full bg-white px-9 py-4 text-sm font-bold text-neutral-900 shadow-2xl shadow-black/30 transition hover:scale-105">
                            Get Started — It's Free
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                        <Link href="/templates" class="inline-flex items-center justify-center rounded-full border border-white/25 px-9 py-4 text-sm font-semibold text-white/80 backdrop-blur-sm transition hover:border-white/50 hover:text-white">
                            See Templates
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
/* Scroll reveal */
[data-animate] {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.85s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.85s cubic-bezier(0.22, 1, 0.36, 1);
}
[data-animate].is-visible { opacity: 1; transform: none; }

/* ── Parallax collage cards — soft reveal on the wrapper (keeps inner tilt) ── */
.pcard {
    opacity: 0;
    transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1),
                transform 1s cubic-bezier(0.22, 1, 0.36, 1);
}
.pcard-up   { transform: translateY(56px) scale(0.96); }
.pcard-down { transform: translateY(-44px) scale(0.96); }
.pcard.is-visible { opacity: 1 !important; transform: none !important; }

/* Category emoji idle float (paused on hover so the hover pop reads cleanly) */
@keyframes cat-float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50%       { transform: translateY(-7px) rotate(-4deg); }
}
.cat-emoji { animation: cat-float 4s ease-in-out infinite; }
.cat-card:hover .cat-emoji { animation-play-state: paused; }

/* Respect reduced motion for local animations */
@media (prefers-reduced-motion: reduce) {
    [data-animate], .pcard { opacity: 1 !important; transform: none !important; transition: none; }
    .cat-emoji { animation: none; }
}
</style>
