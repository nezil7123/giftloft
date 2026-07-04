<script setup>
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const props = defineProps({
    form: { type: Object, required: true },
    eventTypes: { type: Object, required: true },
    initialCoverUrl: { type: String, default: null },
});

const inputClass =
    'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

// ── Cover photo upload ────────────────────────────────────
const coverPreview = ref(props.initialCoverUrl);
const onCoverChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    props.form.cover_photo = file;
    props.form.remove_cover_photo = false;
    coverPreview.value = URL.createObjectURL(file);
};
const removeCover = () => {
    props.form.cover_photo = null;
    props.form.remove_cover_photo = true;
    coverPreview.value = null;
};
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
            <label class="text-sm font-semibold text-neutral-800">Cover photo</label>
            <div class="mt-1.5 flex items-center gap-4">
                <div class="h-16 w-24 shrink-0 overflow-hidden rounded-xl bg-neutral-100 ring-1 ring-neutral-200">
                    <img v-if="coverPreview" :src="coverPreview" class="h-full w-full object-cover" alt="" />
                    <div v-else class="flex h-full w-full items-center justify-center text-xl text-neutral-300">🖼️</div>
                </div>
                <div class="flex items-center gap-3">
                    <label class="inline-flex w-fit cursor-pointer items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-neutral-800">
                        <input type="file" accept="image/*" class="hidden" @change="onCoverChange" />
                        {{ coverPreview ? 'Change photo' : 'Upload photo' }}
                    </label>
                    <button v-if="coverPreview" type="button" @click="removeCover" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                </div>
            </div>
            <InputError class="mt-1.5" :message="form.errors.cover_photo" />
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
