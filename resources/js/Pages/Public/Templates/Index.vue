<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import IframeThumb from './IframeThumb.vue';
import { resolveInvitationTemplate } from '@/Templates/registry.js';

const props = defineProps({
    websiteTemplates: { type: Array, required: true },
    invitationTemplates: { type: Array, required: true },
    sample: { type: Object, required: true },
});

// Build a sample event for a given invitation template key.
const invitationSample = (key) => ({ ...props.sample, invitation_template: key });
const invitationComponent = (key) => resolveInvitationTemplate(key);
</script>

<template>
    <PublicLayout>
        <Head title="Templates — Gift Loft" />

        <!-- Hero -->
        <section class="bg-gradient-to-b from-indigo-50 to-white pb-12 pt-32">
            <div class="mx-auto max-w-3xl px-6 text-center sm:px-10">
                <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-indigo-500">Templates</p>
                <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-neutral-950 sm:text-5xl">Beautiful designs for every celebration.</h1>
                <p class="mx-auto mt-5 max-w-xl text-lg leading-8 text-neutral-500">Browse our event website and invitation templates — shown here with sample details. Pick one, add your own story, and publish in minutes.</p>
                <div class="mt-8 flex justify-center gap-3">
                    <a href="#websites" class="rounded-full bg-neutral-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800">Event websites</a>
                    <a href="#invitations" class="rounded-full border border-neutral-300 px-6 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">Invitations</a>
                </div>
            </div>
        </section>

        <!-- Website templates -->
        <section id="websites" class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-neutral-950 sm:text-3xl">Event website templates</h2>
                        <p class="mt-1 text-sm text-neutral-500">A full celebration page guests can visit, with countdown, schedule and registry.</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 sm:inline">{{ websiteTemplates.length }} designs</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="t in websiteTemplates" :key="t.key" class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:shadow-lg">
                        <Link :href="`/templates/website/${t.key}`" class="block">
                            <div class="relative">
                                <IframeThumb :src="`/templates/website/${t.key}?embed=1`" />
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
        <section id="invitations" class="bg-neutral-50 py-20">
            <div class="mx-auto max-w-7xl px-6 sm:px-10 lg:px-12">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-neutral-950 sm:text-3xl">Invitation templates</h2>
                        <p class="mt-1 text-sm text-neutral-500">A shareable invitation card to send your guests.</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 sm:inline">{{ invitationTemplates.length }} designs</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="t in invitationTemplates" :key="t.key" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                        <!-- live invitation card, scaled to fit -->
                        <div class="flex h-[360px] items-center justify-center overflow-hidden bg-gradient-to-br from-neutral-100 to-neutral-200 p-6">
                            <div class="origin-center scale-[0.72]">
                                <component :is="invitationComponent(t.key)" :event="invitationSample(t.key)" />
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
