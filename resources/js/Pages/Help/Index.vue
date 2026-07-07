<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    guides: { type: Object, required: true },
});

const guideList = computed(() => Object.values(props.guides));
const stagger = ['gl-d1', 'gl-d2', 'gl-d3', 'gl-d4'];
</script>

<template>
    <Head title="How it works — Gift Loft" />

    <PublicLayout theme="light">
        <div class="mx-auto max-w-5xl px-6 pb-20 pt-28 sm:px-8 sm:pt-32">
            <!-- Hero -->
            <div class="max-w-2xl">
                <p class="gl-enter text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Getting started</p>
                <h1 class="gl-enter gl-d1 mt-4 text-4xl font-semibold leading-[0.95] tracking-tight text-neutral-950 sm:text-6xl">
                    How Gift Loft works
                </h1>
                <p class="gl-enter gl-d2 mt-5 max-w-xl text-base leading-7 text-neutral-500 sm:text-lg">
                    Four short, illustrated guides — from building a wishlist to sending a gift and
                    designing an invitation. Everything you can do here, in a couple of minutes.
                </p>
            </div>

            <!-- Guide cards -->
            <div class="mt-14 grid gap-5 sm:grid-cols-2">
                <Link
                    v-for="(guide, i) in guideList"
                    :key="guide.slug"
                    :href="route('help.show', guide.slug)"
                    :class="['group gl-enter overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-xl', stagger[i % stagger.length]]"
                >
                    <div class="flex items-center gap-4 bg-gradient-to-br p-6 text-white" :class="guide.gradient">
                        <span class="text-3xl transition-transform duration-300 group-hover:scale-110">{{ guide.emoji }}</span>
                        <p class="text-lg font-bold">{{ guide.title }}</p>
                    </div>
                    <div class="p-6">
                        <p class="text-sm leading-6 text-neutral-500">{{ guide.subtitle }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 transition-all duration-300 group-hover:gap-2">
                            See how
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Sign-up nudge -->
            <div class="mt-16 flex flex-col items-start gap-4 rounded-3xl bg-neutral-950 p-8 text-white sm:flex-row sm:items-center sm:justify-between sm:p-10">
                <div>
                    <p class="text-xl font-semibold tracking-tight">Ready to try it yourself?</p>
                    <p class="mt-1 text-sm text-white/60">Create your first event or wishlist — it's free.</p>
                </div>
                <Link href="/register" class="gl-btn inline-flex shrink-0 items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-950 transition hover:scale-105">
                    Get started
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
