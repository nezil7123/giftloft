<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { resolveWebsiteTemplate } from '@/Templates/registry.js';
import { computed } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    isAuthenticated: { type: Boolean, default: false },
});

const template = computed(() => resolveWebsiteTemplate(props.event.template));

// Light-canvas templates need dark nav text; dark ones keep the white glass.
const LIGHT_TEMPLATES = ['modern', 'botanical', 'storybook'];
const navTheme = computed(() => (LIGHT_TEMPLATES.includes(props.event.template) ? 'light' : 'dark'));
</script>

<template>
    <PublicLayout :theme="navTheme">
        <Head :title="event.title" />
        <component :is="template" :event="event" />
    </PublicLayout>
</template>
