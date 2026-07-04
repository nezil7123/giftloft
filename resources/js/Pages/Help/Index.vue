<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    guides: { type: Object, required: true },
});

const guideList = computed(() => Object.values(props.guides));
</script>

<template>
    <Head title="How it works" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">How it works</h2>
                <p class="text-sm text-neutral-500">A quick, illustrated look at everything you can do on Gift Loft.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <Link v-for="guide in guideList" :key="guide.slug" :href="route('help.show', guide.slug)"
                        class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center gap-4 bg-gradient-to-br p-6 text-white" :class="guide.gradient">
                            <span class="text-3xl">{{ guide.emoji }}</span>
                            <p class="text-lg font-bold">{{ guide.title }}</p>
                        </div>
                        <div class="p-6">
                            <p class="text-sm leading-6 text-neutral-500">{{ guide.subtitle }}</p>
                            <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 opacity-0 transition group-hover:opacity-100">
                                See how
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
