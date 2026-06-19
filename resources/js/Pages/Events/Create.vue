<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EventFields from './Partials/EventFields.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    eventTypes: { type: Object, required: true },
});

const form = useForm({
    title: '',
    type: '',
    description: '',
    starts_at: '',
    ends_at: '',
    location: '',
    venue: '',
    cover_photo_url: '',
    is_public: true,
    status: 'draft',
});

const submit = () => form.post(route('events.store'));
</script>

<template>
    <Head title="Create Event" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('events.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to events</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Create a new event</h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                    <EventFields :form="form" :event-types="eventTypes" />

                    <div class="mt-8 flex items-center justify-end gap-3 border-t border-neutral-200 pt-6">
                        <Link :href="route('events.index')" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating…</span>
                            <span v-else>Create event</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
