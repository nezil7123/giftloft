<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    wishlists: { type: Array, default: () => [] },
});

const hasWishlists = computed(() => props.wishlists.length > 0);

const visBadge = {
    public: 'bg-emerald-100 text-emerald-700',
    private: 'bg-neutral-200 text-neutral-700',
    hidden: 'bg-amber-100 text-amber-700',
};
</script>

<template>
    <Head title="Wishlists" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-neutral-900">My Wishlists</h2>
                    <p class="text-sm text-neutral-500">Build registries your guests can shop from.</p>
                </div>
                <Link :href="route('wishlists.create')" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    New Wishlist
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="hasWishlists" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="wishlist in wishlists"
                        :key="wishlist.id"
                        :href="route('wishlists.show', wishlist.id)"
                        class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-base font-bold text-neutral-900 group-hover:text-indigo-600">{{ wishlist.name }}</h3>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="visBadge[wishlist.visibility]">{{ wishlist.visibility }}</span>
                        </div>
                        <p v-if="wishlist.event" class="mt-1 text-xs text-indigo-500">↪ {{ wishlist.event.title }}</p>
                        <p class="mt-3 line-clamp-2 text-sm text-neutral-500">{{ wishlist.description || 'No description yet.' }}</p>
                        <div class="mt-4 flex items-center gap-2 text-xs">
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700">{{ wishlist.items_count ?? 0 }} items</span>
                            <span v-if="!wishlist.active" class="rounded-full bg-neutral-100 px-2.5 py-1 font-semibold text-neutral-500">Inactive</span>
                        </div>
                    </Link>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <p class="mt-5 text-lg font-bold text-neutral-900">No wishlists yet</p>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-neutral-500">Create a registry, add the gifts you'd love, and share it with your guests.</p>
                    <Link :href="route('wishlists.create')" class="mt-6 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Create a Wishlist</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
