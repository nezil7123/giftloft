<script setup>
import InputError from '@/Components/InputError.vue';

defineProps({
    form: { type: Object, required: true },
    eventTypes: { type: Object, required: true },
});

const inputClass =
    'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
</script>

<template>
    <div class="space-y-6">
        <!-- Title -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Event title <span class="text-rose-500">*</span></label>
            <input
                v-model="form.title"
                type="text"
                :class="inputClass"
                placeholder="e.g. Sarah & James's Wedding"
                autofocus
            />
            <InputError class="mt-1.5" :message="form.errors.title" />
        </div>

        <!-- Type -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Event type <span class="text-rose-500">*</span></label>
            <select v-model="form.type" :class="inputClass">
                <option value="" disabled>Choose a type…</option>
                <option v-for="(label, key) in eventTypes" :key="key" :value="key">{{ label }}</option>
            </select>
            <InputError class="mt-1.5" :message="form.errors.type" />
        </div>

        <!-- Description -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Description</label>
            <textarea
                v-model="form.description"
                rows="4"
                :class="inputClass"
                placeholder="Tell your guests about the celebration…"
            ></textarea>
            <InputError class="mt-1.5" :message="form.errors.description" />
        </div>

        <!-- Dates -->
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-800">Starts at</label>
                <input v-model="form.starts_at" type="datetime-local" :class="inputClass" />
                <InputError class="mt-1.5" :message="form.errors.starts_at" />
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-800">Ends at</label>
                <input v-model="form.ends_at" type="datetime-local" :class="inputClass" />
                <InputError class="mt-1.5" :message="form.errors.ends_at" />
            </div>
        </div>

        <!-- Location / venue -->
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-800">Location</label>
                <input v-model="form.location" type="text" :class="inputClass" placeholder="City, country" />
                <InputError class="mt-1.5" :message="form.errors.location" />
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-800">Venue</label>
                <input v-model="form.venue" type="text" :class="inputClass" placeholder="Venue name" />
                <InputError class="mt-1.5" :message="form.errors.venue" />
            </div>
        </div>

        <!-- Cover photo -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Cover photo URL</label>
            <input v-model="form.cover_photo_url" type="url" :class="inputClass" placeholder="https://…" />
            <InputError class="mt-1.5" :message="form.errors.cover_photo_url" />
        </div>

        <!-- Visibility + status -->
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-800">Status</label>
                <select v-model="form.status" :class="inputClass">
                    <option value="draft">Draft (only you can see it)</option>
                    <option value="published">Published (live)</option>
                </select>
                <InputError class="mt-1.5" :message="form.errors.status" />
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-800">Visibility</label>
                <label class="mt-1.5 flex cursor-pointer items-center gap-3 rounded-xl border border-neutral-300 px-4 py-2.5">
                    <input v-model="form.is_public" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-sm text-neutral-700">Public — anyone with the link can view</span>
                </label>
                <InputError class="mt-1.5" :message="form.errors.is_public" />
            </div>
        </div>
    </div>
</template>
