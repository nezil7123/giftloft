<script setup>
import { ref, computed, watch } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

// Flash messages
const page = usePage();
const flash = computed(() => page.props.flash ?? {});
const showFlash = ref(false);
watch(
    () => flash.value.success,
    (msg) => {
        if (msg) {
            showFlash.value = true;
            setTimeout(() => (showFlash.value = false), 3500);
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <nav class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-sm font-bold text-white shadow-sm">G</div>
                                <span class="hidden text-sm font-semibold tracking-tight text-slate-950 sm:block">Gift Loft</span>
                            </Link>
                        </div>

                        <!-- Desktop nav links -->
                        <div class="hidden space-x-1 sm:ms-8 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
                            <NavLink :href="route('events.index')" :active="route().current('events.*')">Events</NavLink>
                            <NavLink :href="route('wishlists.index')" :active="route().current('wishlists.*')">Wishlists</NavLink>
                            <NavLink :href="route('gifts.index')" :active="route().current('gifts.*')">Gifts</NavLink>
                            <template v-if="$page.props.auth.user.is_admin">
                                <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">Admin</NavLink>
                            </template>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    {{ $page.props.auth.user.name }}
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Mobile hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('events.index')" :active="route().current('events.*')">Events</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('wishlists.index')" :active="route().current('wishlists.*')">Wishlists</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('gifts.index')" :active="route().current('gifts.*')">Gifts</ResponsiveNavLink>
                    <template v-if="$page.props.auth.user.is_admin">
                        <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">Admin</ResponsiveNavLink>
                    </template>
                </div>
                <div class="border-t border-slate-200 pb-3 pt-4">
                    <div class="px-4">
                        <div class="text-base font-medium text-slate-800">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm text-slate-500">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page heading slot -->
        <header v-if="$slots.header" class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page content -->
        <!-- Flash toast -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-3"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 -translate-y-3"
        >
            <div v-if="showFlash" class="fixed left-1/2 top-20 z-50 -translate-x-1/2">
                <div class="flex items-center gap-2.5 rounded-full bg-neutral-900 px-5 py-3 text-sm font-medium text-white shadow-lg">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    {{ flash.success }}
                </div>
            </div>
        </Transition>

        <main>
            <slot />
        </main>
    </div>
</template>
