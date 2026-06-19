<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    stats: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Admin Dashboard</h2>
                    <p class="text-sm text-gray-500">Monitor platform activity and manage the community.</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Total users</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.users }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Active events</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.events }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Gifts</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.gifts }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Puzzles sent</p>
                        <p class="mt-4 text-3xl font-semibold text-gray-900">{{ stats.puzzles }}</p>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-lg font-semibold text-gray-900">Admin actions</p>
                    <p class="mt-2 text-sm text-gray-500">Use this dashboard to review reported content, manage users, and prepare event summaries for the site.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
