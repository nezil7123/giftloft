<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Welcome back, {{ user.name }}</h2>
                    <p class="text-sm text-gray-500">Your event and gifting dashboard is ready.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link
                        href="/events"
                        class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Events
                    </Link>
                    <Link
                        href="/wishlists"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                    >
                        Wishlists
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Events</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.events }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Wishlists</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.wishlists }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Gifts</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.gifts }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Messages</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.messages }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Puzzles</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.puzzles }}</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Event planner</h3>
                        <p class="mt-2 text-sm text-gray-500">Create events, invite guests, and publish your gift registry.</p>
                        <Link href="/events" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            View events
                        </Link>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Wishlist hub</h3>
                        <p class="mt-2 text-sm text-gray-500">Build public and private wishlists for every celebration.</p>
                        <Link href="/wishlists" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Manage wishlists
                        </Link>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Gift tracker</h3>
                        <p class="mt-2 text-sm text-gray-500">Review purchased gifts and payment history.</p>
                        <Link href="/gifts" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Track gifts
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
