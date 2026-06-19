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
</script>

<template>
    <PublicLayout>
        <Head :title="event.title" />
        <component :is="template" :event="event" />
    </PublicLayout>
</template>
