<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// theme: 'dark' for pages that open on a dark hero (white nav text),
// 'light' for pages that open on a light background (dark nav text).
const props = defineProps({ theme: { type: String, default: 'dark' } });

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const mobileOpen     = ref(false);
const activeDropdown = ref(null);
const hidden         = ref(false);
const scrolled       = ref(false);

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
        if (hidden.value) { activeDropdown.value = null; mobileOpen.value = false; }
        lastY = y;
        ticking = false;
    });
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

function toggleDropdown(name) {
    activeDropdown.value = activeDropdown.value === name ? null : name;
}
function closeDropdowns() {
    activeDropdown.value = null;
}

const navGroups = [
    {
        label: 'Plan & Invite',
        key: 'plan',
        items: [
            { label: 'Create Event',      href: '/register' },
            { label: 'Wedding Website',   href: '/register' },
            { label: 'Create Invitation', href: '/register' },
            { label: 'Make a Proposal',   href: '/register' },
        ],
    },
    {
        label: 'Gift Registry',
        key: 'registry',
        items: [
            { label: 'Create a Registry', href: '/register' },
            { label: 'Browse the Shop',   href: '/shop' },
            { label: 'Track Gifts',       href: '/register' },
        ],
    },
    {
        label: 'Shop',
        key: 'shop',
        items: [
            { label: 'All Gifts',      href: '/shop' },
            { label: 'Wedding Gifts',  href: '/shop?category=wedding' },
            { label: 'Baby Gifts',     href: '/shop?category=baby' },
            { label: 'Birthday Gifts', href: '/shop?category=birthday' },
            { label: 'Under ₹499',     href: '/shop?max_price=499' },
        ],
    },
];
</script>

<template>
    <!-- --nav-offset lets sticky elements on the page tuck under the nav and follow it when it hides -->
    <div class="flex min-h-screen flex-col bg-white text-neutral-950" :style="{ '--nav-offset': hidden ? '0px' : '62px' }" @click="closeDropdowns">

        <!-- ── Liquid Glass Nav ── -->
        <nav
            class="nav-glass fixed inset-x-0 top-0 z-50 transition-all duration-500 ease-in-out"
            :class="[hidden ? '-translate-y-full' : 'translate-y-0', theme === 'light' ? 'nav-light' : 'nav-dark', { 'nav-scrolled': scrolled }]"
            @click.stop
        >
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="flex h-[62px] items-center justify-between gap-8">

                    <!-- Logo -->
                    <Link href="/" class="flex shrink-0 items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-indigo-500 text-sm font-bold text-white shadow-sm">G</div>
                        <span class="nav-strong text-base font-semibold tracking-tight">Gift Loft</span>
                    </Link>

                    <!-- Desktop nav groups -->
                    <div class="hidden flex-1 items-center gap-0.5 sm:flex">
                        <div v-for="group in navGroups" :key="group.key" class="relative">
                            <button
                                @click="toggleDropdown(group.key)"
                                class="nav-item nav-chip flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
                                :class="{ 'nav-chip-active': activeDropdown === group.key }"
                            >
                                {{ group.label }}
                                <svg
                                    class="h-3 w-3 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeDropdown === group.key }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown panel -->
                            <Transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="opacity-0 scale-95 translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-if="activeDropdown === group.key"
                                    class="absolute left-0 top-full mt-2 w-52 origin-top-left overflow-hidden rounded-2xl border border-neutral-200/80 bg-white/90 shadow-2xl shadow-black/10 backdrop-blur-2xl backdrop-saturate-200"
                                >
                                    <div class="p-1.5">
                                        <Link
                                            v-for="item in group.items"
                                            :key="item.label"
                                            :href="item.href"
                                            @click="closeDropdowns"
                                            class="block rounded-xl px-4 py-2.5 text-sm text-neutral-700 transition hover:bg-neutral-100 hover:text-neutral-950"
                                        >
                                            {{ item.label }}
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <Link
                            href="/templates"
                            class="nav-item nav-chip rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
                        >Templates</Link>
                    </div>

                    <!-- Desktop right actions -->
                    <div class="hidden items-center gap-4 sm:flex">
                        <template v-if="user">
                            <Link
                                :href="route('dashboard')"
                                class="nav-item text-sm font-medium transition-colors"
                            >Dashboard</Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                class="nav-item text-sm font-medium transition-colors"
                            >Log in</Link>
                            <Link
                                href="/register"
                                class="rounded-full px-5 py-2 text-sm font-semibold transition"
                                :class="theme === 'light' ? 'bg-neutral-950 text-white hover:bg-neutral-800' : 'bg-white text-neutral-900 hover:bg-white/90'"
                            >Get Started</Link>
                        </template>
                    </div>

                    <!-- Mobile hamburger -->
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="nav-item nav-chip rounded-xl p-2 transition sm:hidden"
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
                        <div v-for="group in navGroups" :key="group.key" class="mb-5">
                            <p
                                class="nav-dim mb-2 px-1 text-[10px] font-semibold uppercase tracking-widest"
                            >{{ group.label }}</p>
                            <div class="flex flex-col gap-0.5">
                                <Link
                                    v-for="item in group.items"
                                    :key="item.label"
                                    :href="item.href"
                                    @click="mobileOpen = false"
                                    class="nav-item nav-chip rounded-xl px-3 py-2.5 text-sm transition"
                                >{{ item.label }}</Link>
                            </div>
                        </div>
                        <Link href="/templates" @click="mobileOpen = false" class="nav-item nav-chip block rounded-xl px-3 py-2.5 text-sm font-medium transition">Templates</Link>
                        <div class="nav-border-t mt-5 flex flex-col gap-3 pt-5">
                            <template v-if="user">
                                <Link :href="route('dashboard')" class="nav-item text-sm font-medium">Dashboard</Link>
                            </template>
                            <template v-else>
                                <Link href="/login" class="nav-item text-sm font-medium">Log in</Link>
                                <Link href="/register" class="inline-flex items-center justify-center rounded-full px-5 py-2.5 text-sm font-semibold"
                                    :class="theme === 'light' ? 'bg-neutral-950 text-white' : 'bg-white text-neutral-900'">Get Started</Link>
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
                        <p class="mt-3 max-w-xs text-xs leading-5 text-white/40">The most beautiful way to celebrate and gift — events, registries, and shopping in one place.</p>
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
.nav-chip-active { background: var(--nav-active-bg); color: var(--nav-fg-strong); }
.nav-strong { color: var(--nav-fg-strong); text-shadow: var(--nav-shadow); }
.nav-dim { color: var(--nav-fg-dim); }
.nav-border-t { border-top: 1px solid var(--nav-border); }

/* Mobile dropdown — denser so items stay readable */
.mobile-glass {
    backdrop-filter: blur(40px) saturate(1.8);
    -webkit-backdrop-filter: blur(40px) saturate(1.8);
}
.nav-dark .mobile-glass { background: rgba(10, 10, 20, 0.72); }
.nav-light .mobile-glass { background: rgba(255, 255, 255, 0.92); }
</style>
