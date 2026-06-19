<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({ canLogin: Boolean, canRegister: Boolean });

// ── Celebration categories (full Tailwind class strings so JIT picks them up) ──
const categories = [
    { name: 'Wedding',     emoji: '💍', blurb: 'Celebrate your love story with a site your guests will adore.', grad: 'from-rose-500 to-pink-700',     glow: 'hover:shadow-rose-500/40',    tint: 'text-rose-100/80' },
    { name: 'Birthday',    emoji: '🎂', blurb: 'Make every birthday legendary with your own party page.',       grad: 'from-amber-400 to-orange-600',  glow: 'hover:shadow-amber-500/40',   tint: 'text-amber-100/80' },
    { name: 'Engagement',  emoji: '💞', blurb: 'Announce the big yes and let everyone share the joy.',           grad: 'from-fuchsia-500 to-purple-700', glow: 'hover:shadow-fuchsia-500/40', tint: 'text-fuchsia-100/80' },
    { name: 'Baby Shower', emoji: '👶', blurb: 'Welcome your little one with a sweet registry and page.',        grad: 'from-sky-400 to-blue-700',      glow: 'hover:shadow-sky-500/40',     tint: 'text-sky-100/80' },
    { name: 'Anniversary', emoji: '💕', blurb: 'Mark the years together with a page full of memories.',          grad: 'from-red-400 to-rose-600',      glow: 'hover:shadow-rose-500/40',    tint: 'text-rose-100/80' },
    { name: 'Proposal',    emoji: '💜', blurb: 'Plan the perfect moment and share it beautifully.',              grad: 'from-violet-500 to-purple-800', glow: 'hover:shadow-violet-500/40',  tint: 'text-violet-100/80' },
    { name: 'Graduation',  emoji: '🎓', blurb: 'Celebrate the milestone and gather everyone to cheer.',          grad: 'from-indigo-500 to-blue-700',   glow: 'hover:shadow-indigo-500/40',  tint: 'text-indigo-100/80' },
    { name: 'Housewarming',emoji: '🏡', blurb: 'Open the doors to your new home with a warm invite.',            grad: 'from-emerald-400 to-teal-700',  glow: 'hover:shadow-emerald-500/40', tint: 'text-emerald-100/80' },
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
    <PublicLayout>
        <Head title="Gift Loft — The most beautiful way to celebrate & gift" />

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  HERO                                    ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="relative flex min-h-[100svh] items-center overflow-hidden bg-neutral-950">
            <!-- Gradient wash -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-neutral-950 to-violet-950 opacity-90"></div>
                <div class="blob absolute -left-48 top-0 h-[600px] w-[600px] rounded-full bg-indigo-800/25 blur-3xl"></div>
                <div class="blob blob-b absolute -right-32 bottom-0 h-[500px] w-[500px] rounded-full bg-violet-700/20 blur-3xl"></div>
                <div class="blob blob-c absolute left-1/2 top-1/3 h-96 w-96 -translate-x-1/2 rounded-full bg-rose-700/10 blur-3xl"></div>
            </div>

            <!-- Grid overlay (subtle texture) -->
            <div class="pointer-events-none absolute inset-0 opacity-[0.03]" style="background-image:linear-gradient(white 1px,transparent 1px),linear-gradient(90deg,white 1px,transparent 1px);background-size:60px 60px"></div>

            <div class="relative mx-auto max-w-7xl px-6 py-32 sm:px-10 lg:px-12">
                <div class="grid gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">

                    <!-- Left: copy -->
                    <div class="space-y-9">
                        <div class="inline-flex items-center gap-2.5 rounded-full border border-white/15 bg-white/8 px-4 py-2 backdrop-blur-sm">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_3px_rgba(52,211,153,0.5)]"></span>
                            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/70">Celebrating love, life & every moment</span>
                        </div>

                        <h1 class="text-[3.5rem] font-extrabold leading-[1.06] tracking-[-0.02em] text-white sm:text-6xl lg:text-[4.5rem]">
                            Create a<br/>
                            <span class="bg-gradient-to-r from-indigo-300 via-violet-300 to-pink-300 bg-clip-text text-transparent">
                                celebration
                            </span><br/>
                            they'll never forget.
                        </h1>

                        <p class="max-w-[480px] text-lg leading-8 text-white/55">
                            Beautiful event websites, curated gift registries, and a guest-friendly shop — all in one elegant platform.
                        </p>

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <Link href="/register" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-3.5 text-sm font-bold text-neutral-900 shadow-xl shadow-black/30 transition hover:bg-neutral-100">
                                Get Started Free
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </Link>
                            <Link href="/shop" class="inline-flex items-center justify-center rounded-full border border-white/25 px-8 py-3.5 text-sm font-semibold text-white/80 backdrop-blur-sm transition hover:border-white/50 hover:text-white">
                                Browse the Shop
                            </Link>
                        </div>

                        <!-- Social proof -->
                        <div class="flex items-center gap-4 pt-1">
                            <div class="flex -space-x-2.5">
                                <div class="h-9 w-9 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-rose-400 to-pink-600"></div>
                                <div class="h-9 w-9 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-violet-400 to-purple-600"></div>
                                <div class="h-9 w-9 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-amber-400 to-orange-500"></div>
                                <div class="h-9 w-9 rounded-full ring-2 ring-neutral-950 bg-gradient-to-br from-emerald-400 to-teal-500"></div>
                            </div>
                            <p class="text-sm text-white/45"><span class="font-semibold text-white/75">10,000+</span> events celebrated</p>
                        </div>
                    </div>

                    <!-- Right: floating glass UI mockup -->
                    <div class="relative hidden h-[580px] lg:block">
                        <!-- Main event card -->
                        <div class="float absolute right-0 top-4 w-[290px] overflow-hidden rounded-3xl border border-white/15 bg-white/8 shadow-2xl backdrop-blur-xl">
                            <div class="bg-gradient-to-r from-rose-600/40 to-pink-600/30 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">💍</span>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest text-white/50">Wedding Event</p>
                                        <p class="font-semibold text-white">Sarah & James</p>
                                    </div>
                                    <span class="ml-auto rounded-full bg-emerald-500/30 px-2.5 py-1 text-[10px] font-semibold text-emerald-300">Live</span>
                                </div>
                            </div>
                            <div class="px-6 py-4 space-y-4">
                                <div class="flex gap-2 text-sm text-white/60">
                                    <span>📅 March 15, 2025</span>
                                    <span>·</span>
                                    <span>Mumbai</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/65">124 guests</span>
                                    <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-300">82 RSVP'd</span>
                                </div>
                                <div>
                                    <div class="mb-1.5 flex justify-between text-[10px] text-white/35">
                                        <span>RSVP progress</span><span>66%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-white/10">
                                        <div class="h-1.5 rounded-full bg-gradient-to-r from-emerald-400 to-teal-400" style="width:66%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registry card -->
                        <div class="float-delay absolute left-0 top-56 w-[240px] rounded-3xl border border-white/15 bg-white/8 p-5 shadow-2xl backdrop-blur-xl">
                            <p class="text-[10px] uppercase tracking-widest text-white/45">Gift Registry</p>
                            <div class="mt-3 space-y-2.5">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">🍽 Dinner set</span>
                                    <span class="font-semibold text-white">₹2,499</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">✈️ Weekend trip</span>
                                    <span class="font-semibold text-white">₹8,000</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">📷 Camera</span>
                                    <span class="font-semibold text-white">₹45,000</span>
                                </div>
                            </div>
                            <div class="mt-4 rounded-full bg-indigo-600/50 py-2 text-center text-xs font-semibold text-white">View registry →</div>
                        </div>

                        <!-- Gift purchased -->
                        <div class="float absolute bottom-24 right-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 shadow-xl shadow-emerald-900/40">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xl">🎁</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">Gift purchased!</p>
                                    <p class="text-xs text-emerald-200">Priya sent ₹1,200</p>
                                </div>
                            </div>
                        </div>

                        <!-- Invite sent -->
                        <div class="float-slow absolute bottom-48 right-16 rounded-2xl border border-violet-400/20 bg-violet-600/20 px-4 py-2.5 backdrop-blur-md">
                            <p class="text-xs text-violet-200">✉️ Invite sent to 24 guests</p>
                        </div>
                    </div>
                </div>

                <!-- Scroll hint -->
                <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 flex-col items-center gap-2">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-white/25">Scroll</p>
                    <div class="bounce-y text-white/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  FEATURES — parallax showcase            ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section ref="parallaxSection" class="overflow-hidden bg-gradient-to-b from-white to-neutral-50 py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="grid items-center gap-16 lg:grid-cols-2 lg:gap-20">

                    <!-- LEFT · intro -->
                    <div data-animate class="lg:sticky lg:top-28">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-500">Everything you need</p>
                        <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-5xl">One platform,<br/>endless celebration.</h2>
                        <p class="mt-5 max-w-md text-lg leading-8 text-neutral-500">Beautiful, free, and uniquely yours — with clever features for every step from the first invite to the very last gift.</p>

                        <div class="mt-8 flex flex-wrap gap-2.5">
                            <span class="rounded-full bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-600 ring-1 ring-rose-100">Events</span>
                            <span class="rounded-full bg-indigo-50 px-3.5 py-1.5 text-xs font-semibold text-indigo-600 ring-1 ring-indigo-100">Registry</span>
                            <span class="rounded-full bg-amber-50 px-3.5 py-1.5 text-xs font-semibold text-amber-600 ring-1 ring-amber-100">Shopping</span>
                            <span class="rounded-full bg-violet-50 px-3.5 py-1.5 text-xs font-semibold text-violet-600 ring-1 ring-violet-100">Surprises</span>
                            <span class="rounded-full bg-fuchsia-50 px-3.5 py-1.5 text-xs font-semibold text-fuchsia-600 ring-1 ring-fuchsia-100">Secrets</span>
                            <span class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-semibold text-emerald-600 ring-1 ring-emerald-100">Collections</span>
                        </div>

                        <Link href="/register" class="mt-10 inline-flex items-center gap-2 rounded-full bg-neutral-950 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-neutral-800">
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

                            <!-- 3 · Direct Gift Shopping (amber) -->
                            <div data-animate class="pcard pcard-up" style="transition-delay:.18s">
                                <div class="group rotate-[1.5deg] overflow-hidden rounded-3xl bg-gradient-to-br from-amber-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(245,158,11,0.35)] ring-1 ring-amber-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-amber-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-500 ring-1 ring-amber-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-amber-500">Direct Gift Shopping</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Shop the Feeling</h3>
                                </div>
                            </div>

                            <!-- 5 · Ciphered Messages (fuchsia) -->
                            <div data-animate class="pcard pcard-up" style="transition-delay:.3s">
                                <div class="group rotate-[-1deg] overflow-hidden rounded-3xl bg-gradient-to-br from-fuchsia-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(217,70,239,0.35)] ring-1 ring-fuchsia-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-fuchsia-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-fuchsia-100 text-fuchsia-500 ring-1 ring-fuchsia-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-fuchsia-500">Ciphered Messages &amp; Proposals</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Speak in Secrets</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Track B (drifts down, offset start) -->
                        <div ref="trackB" class="space-y-4 pt-10 will-change-transform sm:space-y-5 sm:pt-16">

                            <!-- 2 · Gift Registry & Wishlist (indigo) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.1s">
                                <div class="group rotate-[2deg] overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(99,102,241,0.35)] ring-1 ring-indigo-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-500 ring-1 ring-indigo-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12M3.75 7.5h16.5a1.5 1.5 0 011.5 1.5v.75H2.25V9a1.5 1.5 0 011.5-1.5zM12 7.5v12" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-500">Gift Registry &amp; Wishlist</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Wish It. List It. Get It.</h3>
                                </div>
                            </div>

                            <!-- 4 · Surprise Gifting (violet) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.22s">
                                <div class="group rotate-[-1.5deg] overflow-hidden rounded-3xl bg-gradient-to-br from-violet-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(139,92,246,0.35)] ring-1 ring-violet-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-violet-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-500 ring-1 ring-violet-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-violet-500">Surprise Gifting</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">The Art of the Surprise</h3>
                                </div>
                            </div>

                            <!-- 6 · Curated Gift Collections (emerald) -->
                            <div data-animate class="pcard pcard-down" style="transition-delay:.34s">
                                <div class="group rotate-[1deg] overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 via-white to-white p-6 shadow-[0_24px_60px_-24px_rgba(16,185,129,0.35)] ring-1 ring-emerald-100/80 transition-all duration-500 hover:rotate-0 hover:-translate-y-1">
                                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-emerald-200/40 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
                                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-500 ring-1 ring-emerald-200/70 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" /></svg>
                                    </div>
                                    <p class="relative mt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-500">Curated Gift Collections</p>
                                    <h3 class="relative mt-1.5 text-lg font-bold tracking-tight text-neutral-900">Gifts, Handpicked for Them</h3>
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
        <section class="bg-white py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-xl text-center">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-500">Made for every moment</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-5xl">Every celebration, beautifully done.</h2>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="(cat, i) in categories"
                        :key="cat.name"
                        href="/register"
                        data-animate
                        :style="`transition-delay:${i * 0.06}s`"
                        class="cat-card group relative block cursor-pointer overflow-hidden rounded-[1.75rem] bg-gradient-to-br p-8 shadow-lg transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl"
                        :class="[cat.grad, cat.glow]"
                    >
                        <!-- shine sweep -->
                        <div class="pointer-events-none absolute inset-0 -translate-x-[120%] -skew-x-12 bg-gradient-to-r from-transparent via-white/25 to-transparent transition-transform duration-[900ms] ease-out group-hover:translate-x-[120%]"></div>
                        <!-- scaling glow blob -->
                        <div class="pointer-events-none absolute -bottom-12 -right-12 h-40 w-40 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-[2.4]"></div>

                        <!-- floating emoji badge -->
                        <div class="cat-emoji relative flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-4xl ring-1 ring-white/25 backdrop-blur-sm transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-12" :style="`animation-delay:${i * 0.35}s`">
                            {{ cat.emoji }}
                        </div>

                        <h3 class="relative mt-6 text-2xl font-extrabold tracking-tight text-white">{{ cat.name }}</h3>
                        <p class="relative mt-2 text-sm leading-6" :class="cat.tint">{{ cat.blurb }}</p>
                        <span class="relative mt-5 inline-flex items-center gap-1.5 text-xs font-semibold text-white/75 transition group-hover:text-white">
                            Get started
                            <svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </span>
                    </Link>
                </div>
            </div>

        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  HOW IT WORKS (dark)                     ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="bg-neutral-950 py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-xl text-center">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-violet-400">Simple as 1 – 2 – 3</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">How Gift Loft works.</h2>
                </div>

                <div class="mt-14 grid gap-5 lg:grid-cols-3">
                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/8 bg-white/[0.04] p-10 transition hover:border-white/15 hover:bg-white/[0.06]" style="transition-delay:.05s">
                        <p class="bg-gradient-to-br from-violet-400 to-purple-600 bg-clip-text text-8xl font-black leading-none text-transparent">01</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Create your event</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Sign up, choose your event type — wedding, birthday, proposal — and have a beautiful page live in minutes.</p>
                        <div class="absolute right-7 top-7 text-4xl opacity-10 transition group-hover:opacity-25">🎉</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-violet-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>
                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/8 bg-white/[0.04] p-10 transition hover:border-white/15 hover:bg-white/[0.06]" style="transition-delay:.1s">
                        <p class="bg-gradient-to-br from-rose-400 to-pink-600 bg-clip-text text-8xl font-black leading-none text-transparent">02</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Build your registry</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Add gifts, experiences, and cash funds. Guests can browse and buy directly — no awkward conversations needed.</p>
                        <div class="absolute right-7 top-7 text-4xl opacity-10 transition group-hover:opacity-25">🎁</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-rose-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>
                    <div data-animate class="group relative overflow-hidden rounded-[1.75rem] border border-white/8 bg-white/[0.04] p-10 transition hover:border-white/15 hover:bg-white/[0.06]" style="transition-delay:.15s">
                        <p class="bg-gradient-to-br from-amber-400 to-orange-500 bg-clip-text text-8xl font-black leading-none text-transparent">03</p>
                        <h3 class="mt-5 text-xl font-bold text-white">Share & celebrate</h3>
                        <p class="mt-3 text-sm leading-7 text-white/50">Share your link, track RSVPs and gifts in real-time, and focus on what matters most — your special day.</p>
                        <div class="absolute right-7 top-7 text-4xl opacity-10 transition group-hover:opacity-25">✨</div>
                        <div class="absolute -bottom-10 -right-10 h-36 w-36 rounded-full bg-amber-600/10 transition-all duration-500 group-hover:scale-[2]"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ╔══════════════════════════════════════════╗ -->
        <!-- ║  TESTIMONIALS                            ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="bg-indigo-50 py-28">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div data-animate class="mx-auto max-w-xl text-center">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-500">Loved by real people</p>
                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-5xl">Real stories, real love.</h2>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-3">
                    <div data-animate class="rounded-[1.75rem] bg-white p-8 shadow-sm ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:shadow-lg" style="transition-delay:.04s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 text-sm leading-7 text-neutral-600">"Our guests couldn't stop complimenting our event page. The registry was so easy to share and everyone loved shopping from it!"</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-rose-400 to-pink-600 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-950">Priya & Arjun</p>
                                <p class="text-xs text-neutral-400">Wedding · Mumbai, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div data-animate class="rounded-[1.75rem] bg-white p-8 shadow-sm ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:shadow-lg" style="transition-delay:.08s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 text-sm leading-7 text-neutral-600">"Setting up our baby shower registry took 15 minutes. The design looked so professional and our family found it super easy to use."</p>
                        <div class="mt-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-950">Sneha & Rohan</p>
                                <p class="text-xs text-neutral-400">Baby Shower · Bangalore, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div data-animate class="rounded-[1.75rem] bg-white p-8 shadow-sm ring-1 ring-neutral-200/60 transition hover:-translate-y-1 hover:shadow-lg" style="transition-delay:.12s">
                        <div class="flex text-base text-amber-400">★★★★★</div>
                        <p class="mt-5 text-sm leading-7 text-neutral-600">"The proposal page I built on Gift Loft was stunning. She said yes and immediately wanted to show everyone the site!"</p>
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
        <!-- ║  FINAL CTA                               ║ -->
        <!-- ╚══════════════════════════════════════════╝ -->
        <section class="relative overflow-hidden bg-indigo-800 py-28">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="blob absolute -left-40 top-0 h-80 w-80 rounded-full bg-violet-600/30 blur-3xl"></div>
                <div class="blob blob-b absolute -right-40 bottom-0 h-80 w-80 rounded-full bg-rose-600/20 blur-3xl"></div>
                <!-- dot grid -->
                <div class="absolute inset-0 opacity-[0.06]" style="background-image:radial-gradient(white 1px,transparent 1px);background-size:28px 28px"></div>
            </div>

            <div data-animate class="relative mx-auto max-w-2xl px-6 py-20 text-center sm:px-10">
                <div class="mb-5 text-6xl">🎊</div>
                <h2 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Your celebration<br/>starts here.</h2>
                <p class="mt-6 text-lg leading-8 text-indigo-200">Join thousands of couples and families who've made their special moments unforgettable with Gift Loft.</p>
                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Link href="/register" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-9 py-4 text-sm font-bold text-indigo-800 shadow-2xl shadow-black/30 transition hover:bg-indigo-50">
                        Get Started — It's Free
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <Link href="/shop" class="inline-flex items-center justify-center rounded-full border border-white/25 px-9 py-4 text-sm font-semibold text-white/80 backdrop-blur-sm transition hover:border-white/50 hover:text-white">
                        Browse the Shop
                    </Link>
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

/* ── Scramble entrance variants (bento feature cards) ── */
.fx {
    opacity: 0;
    will-change: transform, opacity;
    transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}
.fx-up    { transform: translateY(64px); }
.fx-down  { transform: translateY(-44px); }
.fx-left  { transform: translateX(-72px); }
.fx-right { transform: translateX(72px); }
.fx-scale { transform: scale(0.85); }
/* reset on reveal — !important beats every initial variant regardless of order */
.fx.is-visible { opacity: 1 !important; transform: none !important; }

/* ── Card lift + glow on hover ── */
.card {
    transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.4s ease,
                opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}
.card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 30px 60px -25px rgba(15, 23, 42, 0.30);
}

/* ── Parallax collage cards — soft reveal on the wrapper (keeps inner tilt) ── */
.pcard {
    opacity: 0;
    transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1),
                transform 1s cubic-bezier(0.22, 1, 0.36, 1);
}
.pcard-up   { transform: translateY(56px) scale(0.96); }
.pcard-down { transform: translateY(-44px) scale(0.96); }
.pcard.is-visible { opacity: 1 !important; transform: none !important; }

/* Floating hero cards */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-14px); }
}
@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50%       { transform: translateY(-7px) rotate(1deg); }
}
.float       { animation: float      5.5s ease-in-out infinite; }
.float-delay { animation: float      5.5s ease-in-out 2s infinite; }
.float-slow  { animation: float-slow 8s   ease-in-out 1s infinite; }

/* Background blobs */
@keyframes blob {
    0%, 100% { border-radius: 60% 40% 35% 65% / 55% 40% 65% 45%; }
    50%       { border-radius: 35% 60% 65% 40% / 45% 60% 35% 60%; }
}
.blob   { animation: blob 14s ease-in-out infinite; }
.blob-b { animation: blob 14s ease-in-out 5s  infinite; }
.blob-c { animation: blob 18s ease-in-out 9s  infinite; }

/* Scroll indicator */
@keyframes bounce-y {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(8px); }
}
.bounce-y { animation: bounce-y 1.8s ease-in-out infinite; }

/* Category emoji idle float (paused on hover so the hover pop reads cleanly) */
@keyframes cat-float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50%       { transform: translateY(-7px) rotate(-4deg); }
}
.cat-emoji { animation: cat-float 4s ease-in-out infinite; }
.cat-card:hover .cat-emoji { animation-play-state: paused; }
</style>
