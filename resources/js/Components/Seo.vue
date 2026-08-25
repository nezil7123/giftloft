<script setup>
/**
 * Sets the browser-tab title during client-side (SPA) navigation.
 *
 * Everything else — description, canonical, robots, Open Graph, Twitter — is
 * rendered server-side by App\Support\PageSeo via partials/seo.blade.php,
 * because Facebook/WhatsApp/X crawlers never execute JavaScript and so would
 * see none of it if it were emitted from here. Page titles and descriptions
 * are defined in PageSeo, not in these props.
 */
import { Head } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
});
</script>

<template>
    <Head :title="title" />
</template>
