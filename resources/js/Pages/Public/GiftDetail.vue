<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    gift: Object,
    isAuthenticated: Boolean,
});
</script>

<template>
    <PublicLayout>
        <Head title="Gift details — Gift Loft" />

        <div class="mx-auto max-w-5xl px-6 pb-10 pt-24 sm:px-8 lg:px-10">
            <Link href="/shop" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-950">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to shop
            </Link>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1.4fr_0.9fr]">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm shadow-slate-200">
                    <div class="space-y-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.32em] text-indigo-600">Gift details</p>
                                <h1 class="mt-3 text-4xl font-semibold text-slate-950">{{ gift.name }}</h1>
                            </div>
                            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">₹{{ gift.amount ?? '0.00' }}</span>
                        </div>

                        <p class="text-lg leading-8 text-slate-600">{{ gift.description || 'A premium gift offering available for direct purchase.' }}</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Associated event</p>
                                <p class="mt-3 text-sm text-slate-700">{{ gift.event?.title || 'Any celebration' }}</p>
                            </div>
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Gift status</p>
                                <p class="mt-3 text-sm capitalize text-slate-700">{{ gift.status || 'available' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="space-y-6 rounded-[2rem] bg-white p-8 shadow-sm shadow-slate-200">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">Purchase</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Gift Loft makes shopping easy with a polished checkout design coming soon.</p>
                        <div class="mt-6 flex items-center justify-between gap-3">
                            <p class="text-2xl font-semibold text-slate-950">₹{{ gift.amount ?? '0.00' }}</p>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">In stock</span>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6">
                        <h2 class="text-sm font-semibold text-slate-900">Delivery</h2>
                        <p class="text-sm leading-6 text-slate-600">We're preparing a checkout flow that will let guests purchase and send gifts with confidence.</p>
                    </div>

                    <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6">
                        <h2 class="text-sm font-semibold text-slate-900">Your next step</h2>
                        <div class="space-y-3">
                            <template v-if="isAuthenticated">
                                <button disabled class="w-full rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-slate-300">Checkout coming soon</button>
                                <p class="text-sm text-slate-500">Account support is ready. Payment experience coming in the next update.</p>
                            </template>
                            <template v-else>
                                <Link href="/login" class="inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Log in to purchase</Link>
                                <Link href="/register" class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">Create an account</Link>
                            </template>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </PublicLayout>
</template>
