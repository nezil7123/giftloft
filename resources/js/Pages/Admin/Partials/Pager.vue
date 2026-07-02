<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    paginator: { type: Object, required: true }, // Laravel paginator (links, from, to, total)
});
</script>

<template>
    <div v-if="paginator.total > paginator.per_page" class="mt-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-neutral-400">Showing {{ paginator.from }}–{{ paginator.to }} of {{ paginator.total }}</p>
        <div class="flex gap-1">
            <template v-for="(link, i) in paginator.links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                    :class="link.active ? 'bg-neutral-950 text-white' : 'bg-white text-neutral-600 ring-1 ring-neutral-200 hover:bg-neutral-100'"
                    v-html="link.label"
                />
                <span v-else class="rounded-lg px-3 py-1.5 text-xs text-neutral-300" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
