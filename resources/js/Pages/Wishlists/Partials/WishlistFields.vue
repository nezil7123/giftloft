<script setup>
import InputError from '@/Components/InputError.vue';

defineProps({
    form: { type: Object, required: true },
    events: { type: Array, default: () => [] },
});

const inputClass =
    'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
</script>

<template>
    <div class="space-y-6">
        <!-- Name -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Wishlist name <span class="text-rose-500">*</span></label>
            <input v-model="form.name" type="text" :class="inputClass" placeholder="e.g. Honeymoon Fund" autofocus />
            <InputError class="mt-1.5" :message="form.errors.name" />
        </div>

        <!-- Description -->
        <div>
            <label class="text-sm font-semibold text-neutral-800">Description</label>
            <textarea v-model="form.description" rows="3" :class="inputClass" placeholder="What is this wishlist for?"></textarea>
            <InputError class="mt-1.5" :message="form.errors.description" />
        </div>

        <!-- Event link + visibility -->
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-800">Linked event</label>
                <select v-model="form.event_id" :class="inputClass">
                    <option :value="null">None (standalone)</option>
                    <option v-for="event in events" :key="event.id" :value="event.id">{{ event.title }}</option>
                </select>
                <InputError class="mt-1.5" :message="form.errors.event_id" />
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-800">Visibility</label>
                <select v-model="form.visibility" :class="inputClass">
                    <option value="public">Public — shareable link</option>
                    <option value="private">Private — only you</option>
                    <option value="hidden">Hidden — link only, unlisted</option>
                </select>
                <InputError class="mt-1.5" :message="form.errors.visibility" />
            </div>
        </div>

        <!-- Active -->
        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-neutral-300 px-4 py-2.5">
            <input v-model="form.active" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm text-neutral-700">Active — accepting gifts</span>
        </label>
        <InputError :message="form.errors.active" />
    </div>
</template>
