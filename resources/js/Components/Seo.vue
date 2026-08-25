<script setup>
/**
 * Per-page SEO tags. Every public page should render one of these so it gets a
 * unique title + description, an Open Graph/Twitter card, and a canonical URL.
 *
 * Private pages (dashboard, event editing, anything behind auth or a share code)
 * pass `noindex` so they stay out of search results.
 */
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    // Absolute path to the social preview image, resolved against the site root.
    image: { type: String, default: '/brand/comeyay-og.png' },
    type: { type: String, default: 'website' },
    noindex: { type: Boolean, default: false },
});

const page = usePage();
const seo = computed(() => page.props.seo ?? {});

// Titles read "<Page> — ComeYay", except the homepage which already says it.
const fullTitle = computed(() =>
    props.title.includes('ComeYay') ? props.title : `${props.title} — ComeYay`
);
const canonical = computed(() => seo.value.canonical ?? '');
const imageUrl = computed(() => `${seo.value.appUrl ?? ''}${props.image}`);
</script>

<template>
    <Head :title="title">
        <meta v-if="description" head-key="description" name="description" :content="description" />
        <link v-if="canonical" head-key="canonical" rel="canonical" :href="canonical" />
        <meta v-if="noindex" head-key="robots" name="robots" content="noindex, nofollow" />
        <meta v-else head-key="robots" name="robots" content="index, follow, max-image-preview:large" />

        <!-- Open Graph -->
        <meta head-key="og:site_name" property="og:site_name" content="ComeYay" />
        <meta head-key="og:type" property="og:type" :content="type" />
        <meta head-key="og:title" property="og:title" :content="fullTitle" />
        <meta v-if="description" head-key="og:description" property="og:description" :content="description" />
        <meta v-if="canonical" head-key="og:url" property="og:url" :content="canonical" />
        <meta head-key="og:image" property="og:image" :content="imageUrl" />
        <meta head-key="og:image:width" property="og:image:width" content="1200" />
        <meta head-key="og:image:height" property="og:image:height" content="630" />

        <!-- Twitter -->
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="fullTitle" />
        <meta v-if="description" head-key="twitter:description" name="twitter:description" :content="description" />
        <meta head-key="twitter:image" name="twitter:image" :content="imageUrl" />
    </Head>
</template>
