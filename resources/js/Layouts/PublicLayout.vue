<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// theme: 'dark' for pages that open on a dark hero (white nav text),
// 'light' for pages that open on a light background (dark nav text).
const props = defineProps({ theme: { type: String, default: 'dark' } });

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const cartCount = computed(() => page.props.cartCount ?? 0);

const mobileOpen = ref(false);
const hidden     = ref(false);
const scrolled   = ref(false);

let ticking = false;
let lastY = 0;
function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
        const y = window.scrollY;
        scrolled.value = y > 24;
        // hide while scrolling down past the fold; reappear the moment you scroll up
        if (y > lastY + 2 && y > 320) hidden.value = true;
        else if (y < lastY - 2 || y <= 320) hidden.value = false;
        if (hidden.value) mobileOpen.value = false;
        lastY = y;
        ticking = false;
    });
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

// Deliberately minimal: two destinations, everything else lives in the
// footer. Fewer exits from the page = more sign-ups.
const navLinks = [
    { label: 'Shop gifts', href: '/shop' },
    { label: 'Invitations & websites', href: '/templates' },
];
</script>

<template>
    <!-- --nav-offset lets sticky elements on the page tuck under the nav and follow it when it hides -->
    <div class="flex min-h-screen flex-col bg-white text-neutral-950" :style="{ '--nav-offset': hidden ? '0px' : '62px' }">

        <!-- ── Liquid Glass Nav ── -->
        <nav
            class="nav-glass fixed inset-x-0 top-0 z-50 transition-all duration-500 ease-in-out"
            :class="[hidden ? '-translate-y-full' : 'translate-y-0', theme === 'light' ? 'nav-light' : 'nav-dark', { 'nav-scrolled': scrolled }]"
        >
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="flex h-[62px] items-center justify-between gap-8">

                    <!-- Logo -->
                    <Link href="/" class="flex shrink-0 items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-indigo-500 text-sm font-bold text-white shadow-sm">G</div>
                        <span class="nav-strong text-base font-semibold tracking-tight">Gift Loft</span>
                    </Link>

                    <!-- Desktop links — flat, no dropdowns -->
                    <div class="hidden flex-1 items-center gap-1 sm:flex">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            class="nav-item nav-chip rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
                        >{{ link.label }}</Link>
                    </div>

                    <!-- Desktop right actions -->
                    <div class="hidden items-center gap-3 sm:flex">
                        <Link href="/cart" class="nav-item nav-chip relative rounded-xl p-2 transition-all duration-200" aria-label="Cart">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-10 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z" /></svg>
                            <span v-if="cartCount > 0" class="absolute -right-1 -top-1 flex h-4.5 min-w-[1.125rem] items-center justify-center rounded-full bg-indigo-500 px-1 text-[10px] font-bold text-white">{{ cartCount }}</span>
                        </Link>
                        <template v-if="user">
                            <Link
                                :href="route('dashboard')"
                                class="gl-btn group inline-flex items-center gap-1.5 rounded-full bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400"
                            >
                                Dashboard
                                <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                class="nav-item nav-chip rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
                            >Log in</Link>
                            <Link
                                href="/register"
                                class="gl-btn group inline-flex items-center gap-1.5 rounded-full bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03] hover:bg-indigo-400"
                            >
                                Create your event
                                <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile actions -->
                    <div class="flex items-center gap-1 sm:hidden">
                        <Link href="/cart" class="nav-item nav-chip relative rounded-xl p-2 transition" aria-label="Cart">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-10 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z" /></svg>
                            <span v-if="cartCount > 0" class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-indigo-500 px-1 text-[9px] font-bold text-white">{{ cartCount }}</span>
                        </Link>
                        <!-- Mobile hamburger -->
                        <button
                            @click="mobileOpen = !mobileOpen"
                            class="nav-item nav-chip rounded-xl p-2 transition"
                        >
                            <svg v-if="!mobileOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu (glass) -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="mobileOpen"
                    class="mobile-glass nav-border-t sm:hidden"
                >
                    <div class="mx-auto max-w-7xl px-6 py-5">
                        <div class="flex flex-col gap-0.5">
                            <Link
                                v-for="link in navLinks"
                                :key="link.href"
                                :href="link.href"
                                @click="mobileOpen = false"
                                class="nav-item nav-chip rounded-xl px-3 py-3 text-base font-medium transition"
                            >{{ link.label }}</Link>
                        </div>
                        <div class="nav-border-t mt-4 flex flex-col gap-3 pt-4">
                            <template v-if="user">
                                <Link :href="route('dashboard')" @click="mobileOpen = false"
                                    class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25">Go to dashboard</Link>
                            </template>
                            <template v-else>
                                <Link href="/register" @click="mobileOpen = false"
                                    class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25">Create your event — it's free</Link>
                                <Link href="/login" @click="mobileOpen = false" class="nav-item py-1 text-center text-sm font-medium">Log in</Link>
                            </template>
                        </div>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-neutral-200 bg-neutral-900">
            <div class="mx-auto max-w-7xl px-6 py-12 sm:px-10 lg:px-12">
                <div class="flex flex-col gap-8 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <Link href="/" class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-500 text-xs font-bold text-white">G</div>
                            <span class="text-sm font-semibold text-white">Gift Loft</span>
                        </Link>
                        <p class="mt-3 max-w-xs text-xs leading-5 text-white/40">The most beautiful way to celebrate and gift — events, wishlists, and shopping in one place.</p>
                    </div>
                    <div class="flex gap-12">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/40">Plan</p>
                            <div class="mt-3 flex flex-col gap-2">
                                <Link href="/register" class="text-sm text-white/60 transition hover:text-white">Create Event</Link>
                                <Link href="/register" class="text-sm text-white/60 transition hover:text-white">Wedding Website</Link>
                                <Link href="/register" class="text-sm text-white/60 transition hover:text-white">Invitations</Link>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/40">Shop</p>
                            <div class="mt-3 flex flex-col gap-2">
                                <Link href="/shop" class="text-sm text-white/60 transition hover:text-white">All Gifts</Link>
                                <Link href="/shop?category=wedding" class="text-sm text-white/60 transition hover:text-white">Wedding</Link>
                                <Link href="/shop?category=birthday" class="text-sm text-white/60 transition hover:text-white">Birthday</Link>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/40">Account</p>
                            <div class="mt-3 flex flex-col gap-2">
                                <Link href="/login" class="text-sm text-white/60 transition hover:text-white">Log in</Link>
                                <Link href="/register" class="text-sm text-white/60 transition hover:text-white">Register</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-10 flex items-center justify-between border-t border-white/10 pt-6">
                    <p class="text-xs text-white/30">&copy; 2025 Gift Loft. All rights reserved.</p>
                    <p class="text-xs text-white/20">Made with love in India 🇮🇳</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ── Theme tokens — the nav adapts to the page behind it ── */
.nav-dark {
    --nav-fg: rgba(255, 255, 255, 0.82);
    --nav-fg-strong: #ffffff;
    --nav-fg-dim: rgba(255, 255, 255, 0.45);
    --nav-hover-bg: rgba(255, 255, 255, 0.12);
    --nav-active-bg: rgba(255, 255, 255, 0.15);
    --nav-border: rgba(255, 255, 255, 0.12);
    --nav-shadow: 0 1px 4px rgba(0, 0, 0, 0.30);
    background: rgba(255, 255, 255, 0.04); /* nearly invisible over a dark hero */
    box-shadow: inset 0 0.5px 0 rgba(255, 255, 255, 0.24); /* specular catch-light */
}
.nav-dark.nav-scrolled {
    background: rgba(10, 10, 18, 0.62); /* readable over any section once scrolled */
}
.nav-light {
    --nav-fg: rgba(23, 23, 23, 0.72);
    --nav-fg-strong: #0a0a0a;
    --nav-fg-dim: rgba(23, 23, 23, 0.45);
    --nav-hover-bg: rgba(0, 0, 0, 0.06);
    --nav-active-bg: rgba(0, 0, 0, 0.08);
    --nav-border: rgba(0, 0, 0, 0.08);
    --nav-shadow: none;
    background: rgba(255, 255, 255, 0.55);
    box-shadow: inset 0 0.5px 0 rgba(255, 255, 255, 0.6);
}
.nav-light.nav-scrolled {
    background: rgba(255, 255, 255, 0.85);
    box-shadow: 0 8px 30px -18px rgba(0, 0, 0, 0.25);
}

/* ── Pure liquid glass — floats over whatever is below ── */
.nav-glass {
    backdrop-filter: blur(32px) saturate(1.8);
    -webkit-backdrop-filter: blur(32px) saturate(1.8);
    border-bottom: 1px solid var(--nav-border);
}

/* ── Var-driven nav items ── */
.nav-item { color: var(--nav-fg); text-shadow: var(--nav-shadow); }
.nav-item:hover { color: var(--nav-fg-strong); }
.nav-chip:hover { background: var(--nav-hover-bg); }
.nav-strong { color: var(--nav-fg-strong); text-shadow: var(--nav-shadow); }
.nav-border-t { border-top: 1px solid var(--nav-border); }

/* Mobile dropdown — denser so items stay readable */
.mobile-glass {
    backdrop-filter: blur(40px) saturate(1.8);
    -webkit-backdrop-filter: blur(40px) saturate(1.8);
}
.nav-dark .mobile-glass { background: rgba(10, 10, 20, 0.72); }
.nav-light .mobile-glass { background: rgba(255, 255, 255, 0.92); }
</style>
