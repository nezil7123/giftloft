<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    wishlist: { type: Object, required: true },
    isAuthenticated: { type: Boolean, default: false },
});

const money = (v) => (v == null ? null : `₹${Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);

const remaining = (item) => Math.max(0, (item.quantity ?? 1) - (item.claimed_quantity ?? 0));
const isClaimed = (item) => remaining(item) <= 0;

const itemsLeft = computed(() => props.wishlist.items.filter((i) => !isClaimed(i)).length);
</script>

<template>
    <PublicLayout>
        <Head :title="`${wishlist.name} — Registry`" />

        <!-- Header -->
        <section class="bg-gradient-to-br from-indigo-700 via-violet-700 to-purple-800 pb-16 pt-32">
            <div class="mx-auto max-w-4xl px-6 text-center sm:px-10">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">Gift Registry</p>
                <h1 class="mt-4 font-serif text-4xl font-bold text-white sm:text-6xl">{{ wishlist.name }}</h1>
                <p v-if="wishlist.description" class="mx-auto mt-5 max-w-xl text-base leading-7 text-white/80">{{ wishlist.description }}</p>
                <div class="mt-6 flex items-center justify-center gap-3 text-sm text-white/70">
                    <span>Curated by {{ wishlist.user?.name }}</span>
                    <span v-if="wishlist.event">· for {{ wishlist.event.title }}</span>
                </div>
            </div>
        </section>

        <!-- Items -->
        <section class="bg-neutral-50 py-16">
            <div class="mx-auto max-w-5xl px-6 sm:px-10">
                <p class="mb-6 text-sm text-neutral-500">{{ itemsLeft }} of {{ wishlist.items.length }} gifts still available</p>

                <div v-if="wishlist.items.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="item in wishlist.items"
                        :key="item.id"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg"
                        :class="{ 'opacity-60': isClaimed(item) }"
                    >
                        <div class="aspect-[4/3] bg-neutral-100">
                            <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-neutral-300">
                                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12M3.75 7.5h16.5a1.5 1.5 0 011.5 1.5v.75H2.25V9a1.5 1.5 0 011.5-1.5z" /></svg>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-neutral-900">{{ item.title }}</h3>
                                <span v-if="money(item.price)" class="shrink-0 text-sm font-semibold text-neutral-700">{{ money(item.price) }}</span>
                            </div>
                            <p v-if="item.note" class="mt-1 line-clamp-2 text-xs text-neutral-500">{{ item.note }}</p>

                            <div class="mt-4">
                                <span v-if="isClaimed(item)" class="inline-flex w-full items-center justify-center rounded-full bg-neutral-100 px-4 py-2 text-sm font-semibold text-neutral-400">Already gifted 🎁</span>
                                <Link v-else :href="route('checkout.show', item.id)" class="inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Gift this
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                    <p class="text-base font-bold text-neutral-900">This registry is still being prepared</p>
                    <p class="mt-2 text-sm text-neutral-500">Check back soon — the gift list is on its way.</p>
                </div>

                <p class="mt-12 text-center text-sm text-neutral-400">Powered by Gift Loft</p>
            </div>
        </section>
    </PublicLayout>
</template>
