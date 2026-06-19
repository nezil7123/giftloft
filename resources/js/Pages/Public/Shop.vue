<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    gifts: Array,
});
</script>

<template>
    <PublicLayout>
        <Head title="Gift Shop — Gift Loft" />

        <div class="mx-auto max-w-7xl px-6 pb-12 pt-24 sm:px-8 lg:px-10">
            <div class="rounded-[2rem] bg-white p-10 shadow-sm shadow-slate-200">
                <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.32em] text-indigo-600">Gift shop</p>
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Shop gift picks for your event.</h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">Browse a curated selection of public gifts, honeymoons, and experiences all designed for effortless gifting.</p>
                    </div>
                    <Link href="/register" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Create your registry</Link>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                <template v-if="gifts && gifts.length">
                    <div v-for="gift in gifts" :key="gift.id" class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="space-y-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">{{ gift.event?.title || 'Gift item' }}</p>
                                    <h2 class="mt-3 text-2xl font-semibold text-slate-950">{{ gift.name }}</h2>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">₹{{ gift.amount ?? '0.00' }}</span>
                            </div>
                            <p class="text-sm leading-6 text-slate-600">{{ gift.description || 'Thoughtful gift options perfect for your celebration.' }}</p>
                            <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                <div class="rounded-3xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Available now</div>
                                <Link :href="route('public.shop.show', gift.id)" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">View</Link>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="col-span-3 rounded-[2rem] border border-dashed border-slate-300 bg-white px-8 py-16 text-center">
                        <h2 class="text-2xl font-semibold text-slate-950">No gifts available yet</h2>
                        <p class="mt-4 text-sm leading-6 text-slate-600">Add public registry items and let guests shop from your event page.</p>
                        <Link href="/register" class="mt-8 inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Register now</Link>
                    </div>
                </template>
            </div>
        </div>
    </PublicLayout>
</template>
