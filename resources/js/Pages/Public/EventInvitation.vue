<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { resolveInvitationTemplate } from '@/Templates/registry.js';
import { computed } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
});

const template = computed(() => resolveInvitationTemplate(props.event.invitation_template));
const eventUrl = computed(() => (props.event.share_code ? `/e/${props.event.share_code}` : '/'));
</script>

<template>
    <Head :title="`You're invited — ${event.title}`" />

    <div class="flex min-h-screen flex-col items-center justify-center gap-8 bg-neutral-100 px-6 py-16">
        <component :is="template" :event="event" />

        <Link :href="eventUrl" class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-neutral-700">
            View event details &amp; RSVP
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
        </Link>
        <p class="text-xs text-neutral-400">Powered by Gift Loft</p>
    </div>
</template>
