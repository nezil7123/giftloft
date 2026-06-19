<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { resolveWebsiteTemplate } from '@/Templates/registry.js';
import { computed } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    templateKey: { type: String, required: true },
    embed: { type: Boolean, default: false },
});

const template = computed(() => resolveWebsiteTemplate(props.templateKey));
</script>

<template>
    <Head :title="`Template preview — ${templateKey}`" />

    <!-- Embed mode: bare design only (used inside gallery iframes) -->
    <component :is="template" v-if="embed" :event="event" />

    <!-- Full preview: design + sticky preview/CTA bar -->
    <div v-else>
        <div class="sticky top-0 z-50 flex items-center justify-between gap-4 border-b border-white/10 bg-neutral-900/90 px-6 py-3 backdrop-blur-md">
            <Link href="/templates" class="inline-flex items-center gap-1.5 text-sm font-medium text-white/70 transition hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                All templates
            </Link>
            <div class="flex items-center gap-2">
                <span class="hidden text-xs uppercase tracking-widest text-white/40 sm:inline">Sample preview</span>
                <Link href="/register" class="rounded-full bg-white px-5 py-2 text-sm font-semibold text-neutral-900 transition hover:bg-neutral-100">Use this template</Link>
            </div>
        </div>
        <component :is="template" :event="event" />
    </div>
</template>
