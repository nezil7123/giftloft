<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WishlistFields from './Partials/WishlistFields.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    events: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    description: '',
    event_id: null,
    visibility: 'public',
    active: true,
    delivery_address: { recipient_name: '', phone: '', address_line: '', city: '', postal_code: '' },
});

const submit = () => form.post(route('wishlists.store'));
</script>

<template>
    <Head title="New Wishlist" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('wishlists.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to wishlists</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Create a wishlist</h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                    <WishlistFields :form="form" :events="events" />
                    <div class="mt-8 flex items-center justify-end gap-3 border-t border-neutral-200 pt-6">
                        <Link :href="route('wishlists.index')" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                            {{ form.processing ? 'Creating…' : 'Create wishlist' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
