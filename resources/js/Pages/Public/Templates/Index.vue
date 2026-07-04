<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import IframeThumb from './IframeThumb.vue';
import { resolveInvitationTemplate } from '@/Templates/registry.js';
import { ref, computed } from 'vue';

const props = defineProps({
    websiteTemplates: { type: Array, required: true },
    invitationTemplates: { type: Array, required: true },
    eventTypes: { type: Object, required: true },
    samples: { type: Object, required: true },
});

const selectedType = ref('wedding');

const invitationComponent = (key) => resolveInvitationTemplate(key);
// Sample event for the selected type, with the given invitation template applied.
const invitationSample = (key) => ({ ...props.samples[selectedType.value], invitation_template: key });
</script>

<template>
    <PublicLayout theme="light">
        <Head title="Templates — Gift Loft" />

        <!-- Hero -->
        <section class="bg-gradient-to-b from-indigo-50 to-white pb-10 pt-32">
            <div class="mx-auto max-w-3xl px-6 text-center sm:px-10">
                <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-indigo-500">Templates</p>
                <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-5xl">Beautiful designs for every celebration.</h1>
                <p class="mx-auto mt-5 max-w-xl text-lg leading-8 text-neutral-500">Pick an event type to preview our designs with matching sample content, then make it your own in minutes.</p>
            </div>
        </section>

        <!-- Event type filter — dropdown on mobile, chips on larger screens -->
        <div class="sticky top-[var(--nav-offset,0px)] z-30 border-y border-neutral-200 bg-white/85 backdrop-blur-md transition-[top] duration-500 ease-in-out">
            <!-- Mobile: dropdown -->
            <div class="mx-auto flex max-w-7xl items-center gap-3 px-6 py-3 sm:hidden">
                <label for="event-type" class="shrink-0 text-xs font-bold uppercase tracking-widest text-neutral-400">Event type</label>
                <div class="relative flex-1">
                    <select
                        id="event-type"
                        v-model="selectedType"
                        class="w-full appearance-none rounded-xl border-neutral-300 bg-white py-2.5 pl-4 pr-10 text-sm font-semibold text-neutral-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option v-for="(label, key) in eventTypes" :key="key" :value="key">{{ label }}</option>
                    </select>
                    <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            <!-- Desktop: chips -->
            <div class="mx-auto hidden max-w-7xl gap-2 overflow-x-auto px-6 py-3 sm:flex sm:justify-center sm:px-10">
                <button
                    v-for="(label, key) in eventTypes"
                    :key="key"
                    @click="selectedType = key"
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="selectedType === key ? 'bg-neutral-950 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'"
                >{{ label }}</button>
            </div>
        </div>

        <!-- Website templates -->
        <section id="websites" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-neutral-950 sm:text-3xl">{{ eventTypes[selectedType] }} website templates</h2>
                        <p class="mt-1 text-sm text-neutral-500">A full celebration page with countdown, schedule and wishlist.</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 sm:inline">{{ websiteTemplates.length }} designs</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="t in websiteTemplates" :key="t.key" class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:shadow-lg">
                        <Link :href="`/templates/website/${t.key}?type=${selectedType}`" class="block">
                            <div class="relative">
                                <!-- keyed so switching type reloads the themed preview -->
                                <IframeThumb :key="`${t.key}-${selectedType}`" :src="`/templates/website/${t.key}?embed=1&type=${selectedType}`" />
                                <div class="absolute inset-0 flex items-center justify-center bg-neutral-950/0 opacity-0 transition group-hover:bg-neutral-950/30 group-hover:opacity-100">
                                    <span class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-neutral-900 shadow-lg">View full preview</span>
                                </div>
                            </div>
                        </Link>
                        <div class="flex items-start justify-between gap-3 p-5">
                            <div>
                                <h3 class="text-base font-bold text-neutral-900">{{ t.name }}</h3>
                                <p class="mt-1 text-xs leading-5 text-neutral-500">{{ t.description }}</p>
                            </div>
                            <Link href="/register" class="shrink-0 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">Use this</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Invitation templates -->
        <section id="invitations" class="bg-neutral-50 py-16">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-neutral-950 sm:text-3xl">{{ eventTypes[selectedType] }} invitation templates</h2>
                        <p class="mt-1 text-sm text-neutral-500">A shareable invitation card to send your guests.</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 sm:inline">{{ invitationTemplates.length }} designs</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="t in invitationTemplates" :key="t.key" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                        <div class="flex h-[360px] items-center justify-center overflow-hidden bg-gradient-to-br from-neutral-100 to-neutral-200 p-6">
                            <div class="origin-center scale-[0.72]">
                                <component :is="invitationComponent(t.key)" :key="`${t.key}-${selectedType}`" :event="invitationSample(t.key)" />
                            </div>
                        </div>
                        <div class="flex items-start justify-between gap-3 p-5">
                            <div>
                                <h3 class="text-base font-bold text-neutral-900">{{ t.name }}</h3>
                                <p class="mt-1 text-xs leading-5 text-neutral-500">{{ t.description }}</p>
                            </div>
                            <Link href="/register" class="shrink-0 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">Use this</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-white py-20">
            <div class="mx-auto max-w-2xl px-6 text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-neutral-950">Found one you love?</h2>
                <p class="mx-auto mt-3 max-w-md text-neutral-500">Create your event, pick a template, and make it yours — it's free to start.</p>
                <Link href="/register" class="mt-7 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                    Get started free
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
