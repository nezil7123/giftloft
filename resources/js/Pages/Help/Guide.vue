<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    guide: { type: Object, required: true },
});
</script>

<template>
    <Head :title="`${guide.title} — Gift Loft`" />

    <PublicLayout theme="light">
        <div class="pb-20 pt-28 sm:pt-32">
            <div class="mx-auto max-w-4xl px-6 sm:px-8">
                <Link :href="route('help.index')" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-neutral-500 transition hover:text-neutral-900">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                    All guides
                </Link>
                <!-- Hero -->
                <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br p-8 text-white shadow-sm sm:p-10" :class="guide.gradient">
                    <div class="grid gap-8 sm:grid-cols-[1.2fr_1fr] sm:items-center">
                        <div>
                            <span class="text-4xl">{{ guide.emoji }}</span>
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">{{ guide.title }}</h1>
                            <p class="mt-3 max-w-md text-sm leading-6 text-white/85">{{ guide.subtitle }}</p>
                        </div>

                        <!-- Decorative browser-style mockup -->
                        <div class="rounded-2xl bg-white/10 p-2 shadow-lg backdrop-blur-sm ring-1 ring-white/20">
                            <div class="overflow-hidden rounded-xl bg-white/95 text-neutral-900">
                                <div class="flex items-center gap-1.5 border-b border-neutral-100 px-3 py-2">
                                    <span class="h-2 w-2 rounded-full bg-rose-400"></span>
                                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    <span class="ml-2 flex-1 truncate rounded-full bg-neutral-100 px-2.5 py-1 text-center text-[10px] font-medium text-neutral-500">{{ guide.mockupLabel }}</span>
                                </div>
                                <div class="space-y-2 p-4">
                                    <div class="h-2.5 w-2/3 rounded-full bg-gradient-to-r opacity-80" :class="guide.gradient"></div>
                                    <div class="h-2 w-full rounded-full bg-neutral-100"></div>
                                    <div class="h-2 w-5/6 rounded-full bg-neutral-100"></div>
                                    <div class="mt-3 grid grid-cols-3 gap-1.5">
                                        <div class="h-8 rounded-lg bg-gradient-to-br opacity-70" :class="guide.gradient"></div>
                                        <div class="h-8 rounded-lg bg-neutral-100"></div>
                                        <div class="h-8 rounded-lg bg-neutral-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Steps -->
                <div class="relative mt-10">
                    <div class="absolute bottom-6 left-6 top-6 w-px bg-neutral-200 sm:left-7" aria-hidden="true"></div>

                    <div class="space-y-6">
                        <div v-for="(step, i) in guide.steps" :key="step.title" class="relative flex gap-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-200/70 sm:p-6">
                            <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br text-white shadow-sm sm:h-14 sm:w-14" :class="guide.gradient">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="step.icon" /></svg>
                            </div>
                            <div class="min-w-0 pt-0.5">
                                <p class="text-xs font-bold uppercase tracking-widest text-neutral-400">Step {{ i + 1 }}</p>
                                <p class="mt-0.5 text-base font-bold text-neutral-900">{{ step.title }}</p>
                                <p class="mt-1 text-sm leading-6 text-neutral-500">{{ step.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mt-10 rounded-[1.75rem] bg-gradient-to-br p-8 text-center text-white shadow-sm sm:p-10" :class="guide.gradient">
                    <p class="text-lg font-bold">Ready to get started?</p>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                        <Link :href="route(guide.cta.route)" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:bg-white/90">{{ guide.cta.label }}</Link>
                        <Link v-if="guide.secondaryCta" :href="route(guide.secondaryCta.route)" class="inline-flex items-center justify-center rounded-full bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/20">{{ guide.secondaryCta.label }}</Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
