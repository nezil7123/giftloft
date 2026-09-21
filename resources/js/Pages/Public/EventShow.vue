<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Seo from '@/Components/Seo.vue';
import RsvpForm from '@/Components/RsvpForm.vue';
import { resolveWebsiteTemplate } from '@/Templates/registry.js';
import { computed } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    isAuthenticated: { type: Boolean, default: false },
    rsvpSettings: { type: Object, default: () => ({ enabled: true, ask_meal: true, ask_accommodation: true, deadline: null }) },
});

const template = computed(() => resolveWebsiteTemplate(props.event.template));

// Light-canvas templates need dark nav text; dark ones keep the white glass.
const LIGHT_TEMPLATES = ['modern', 'botanical', 'storybook'];
const navTheme = computed(() => (LIGHT_TEMPLATES.includes(props.event.template) ? 'light' : 'dark'));
</script>

<template>
    <PublicLayout :theme="navTheme">
        <Seo :title="event.title" />
        <component :is="template" :event="event" />

        <!-- RSVP lives outside the template so every design gets it, themed to match. -->
        <div :class="navTheme === 'light' ? 'bg-neutral-50' : 'bg-neutral-950'">
            <RsvpForm :event="event" :settings="rsvpSettings" :theme="navTheme === 'light' ? 'light' : 'dark'" />
        </div>
    </PublicLayout>
</template>
