<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EventFields from './Partials/EventFields.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    event: { type: Object, required: true },
    eventTypes: { type: Object, required: true },
});

// Convert an ISO datetime to the value a <input type="datetime-local"> expects (YYYY-MM-DDTHH:mm)
const toLocal = (iso) => {
    if (!iso) return '';
    const d = new Date(iso);
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const form = useForm({
    title: props.event.title ?? '',
    type: props.event.type ?? '',
    description: props.event.description ?? '',
    starts_at: toLocal(props.event.starts_at),
    ends_at: toLocal(props.event.ends_at),
    location: props.event.location ?? '',
    venue: props.event.venue ?? '',
    cover_photo_url: props.event.cover_photo_url ?? '',
    is_public: Boolean(props.event.is_public),
    status: props.event.status ?? 'draft',
});

const submit = () => form.put(route('events.update', props.event.id));

const destroy = () => {
    if (confirm('Delete this event? This cannot be undone.')) {
        router.delete(route('events.destroy', props.event.id));
    }
};
</script>

<template>
    <Head title="Edit Event" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('events.show', event.id)" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to event</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Edit event</h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-neutral-200/70">
                    <EventFields :form="form" :event-types="eventTypes" />

                    <div class="mt-8 flex items-center justify-between gap-3 border-t border-neutral-200 pt-6">
                        <button
                            type="button"
                            @click="destroy"
                            class="rounded-full px-5 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50"
                        >
                            Delete event
                        </button>
                        <div class="flex items-center gap-3">
                            <Link :href="route('events.show', event.id)" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Saving…</span>
                                <span v-else>Save changes</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
