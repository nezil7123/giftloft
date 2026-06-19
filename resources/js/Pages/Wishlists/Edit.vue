<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WishlistFields from './Partials/WishlistFields.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    wishlist: { type: Object, required: true },
    events: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.wishlist.name ?? '',
    description: props.wishlist.description ?? '',
    event_id: props.wishlist.event_id ?? null,
    visibility: props.wishlist.visibility ?? 'public',
    active: Boolean(props.wishlist.active),
});

const submit = () => form.put(route('wishlists.update', props.wishlist.id));

const destroy = () => {
    if (confirm('Delete this wishlist and all its items? This cannot be undone.')) {
        router.delete(route('wishlists.destroy', props.wishlist.id));
    }
};
</script>

<template>
    <Head title="Edit Wishlist" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('wishlists.show', wishlist.id)" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to wishlist</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Edit wishlist</h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                    <WishlistFields :form="form" :events="events" />
                    <div class="mt-8 flex items-center justify-between gap-3 border-t border-neutral-200 pt-6">
                        <button type="button" @click="destroy" class="rounded-full px-5 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                        <div class="flex items-center gap-3">
                            <Link :href="route('wishlists.show', wishlist.id)" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                                {{ form.processing ? 'Saving…' : 'Save changes' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
