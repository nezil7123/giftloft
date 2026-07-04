<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import InputError from '@/Components/InputError.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    addons: { type: Object, required: true }, // { packaging: [...], message_sticker: [...], custom_card: [...] }
    types: { type: Object, required: true },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;

const editing = ref(null); // null = closed, 'new' = create, addon object = edit
const form = useForm({
    type: 'packaging',
    name: '',
    description: '',
    image: null,
    remove_image: false,
    price: '',
    is_default: false,
    is_active: true,
    sort_order: null,
});

const imagePreview = ref(null); // existing image URL or freshly-picked object URL
const fileInput = ref(null);

const openCreate = (type) => {
    form.reset();
    form.clearErrors();
    form.type = type;
    imagePreview.value = null;
    editing.value = 'new';
};
const openEdit = (addon) => {
    form.clearErrors();
    Object.assign(form, {
        type: addon.type, name: addon.name, description: addon.description ?? '',
        image: null, remove_image: false,
        price: addon.price, is_default: !!addon.is_default, is_active: !!addon.is_active,
        sort_order: addon.sort_order,
    });
    imagePreview.value = addon.image ?? null;
    editing.value = addon;
};
const close = () => (editing.value = null);

const onPickImage = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.image = file;
    form.remove_image = false;
    imagePreview.value = URL.createObjectURL(file);
};
const removeImage = () => {
    form.image = null;
    form.remove_image = true;
    imagePreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const save = () => {
    const opts = { preserveScroll: true, onSuccess: close };
    if (editing.value === 'new') form.post(route('admin.gift-addons.store'), opts);
    else form.put(route('admin.gift-addons.update', editing.value.id), opts);
};

const toggleActive = (addon) => {
    router.put(route('admin.gift-addons.update', addon.id), {
        type: addon.type, name: addon.name, description: addon.description,
        price: addon.price, is_default: addon.is_default, sort_order: addon.sort_order,
        is_active: !addon.is_active,
    }, { preserveScroll: true });
};

const confirmingDelete = ref(null);
const destroy = () => {
    router.delete(route('admin.gift-addons.destroy', confirmingDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => (confirmingDelete.value = null),
    });
};

const inputClass = 'block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500';
const labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
</script>

<template>
    <Head title="Admin — Gift Addons" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Gift customization addons</h2>
                <p class="text-sm text-neutral-500">Packaging types, message stickers, and custom cards guests can add when sending a gift — with your own pricing.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <div v-for="(label, type) in types" :key="type" class="mb-8">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-base font-bold text-neutral-900">{{ label }}</h3>
                        <button type="button" @click="openCreate(type)" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add {{ label.toLowerCase() }}
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                        <table class="w-full min-w-[640px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                    <th class="px-6 py-4 font-semibold">Preview</th>
                                    <th class="px-4 py-4 font-semibold">Name</th>
                                    <th class="px-4 py-4 font-semibold">Price</th>
                                    <th class="px-4 py-4 font-semibold">Default</th>
                                    <th class="px-4 py-4 font-semibold">Status</th>
                                    <th class="px-6 py-4 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                <tr v-for="a in (addons[type] ?? [])" :key="a.id" class="transition hover:bg-neutral-50/60">
                                    <td class="px-6 py-3.5">
                                        <div class="h-12 w-12 overflow-hidden rounded-xl bg-neutral-100 ring-1 ring-neutral-200/70">
                                            <img v-if="a.image" :src="a.image" :alt="a.name" class="h-full w-full object-cover" />
                                            <span v-else class="flex h-full w-full items-center justify-center text-lg">🎁</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-semibold text-neutral-900">{{ a.name }}</p>
                                        <p v-if="a.description" class="mt-0.5 max-w-sm truncate text-xs text-neutral-400">{{ a.description }}</p>
                                    </td>
                                    <td class="px-4 py-3.5 font-semibold tabular-nums text-neutral-900">{{ a.price > 0 ? money(a.price) : 'Free' }}</td>
                                    <td class="px-4 py-3.5">
                                        <span v-if="a.is_default" class="rounded-full bg-indigo-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-indigo-700">Default</span>
                                        <span v-else class="text-neutral-300">—</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <button type="button" @click="toggleActive(a)"
                                            class="rounded-full px-3 py-1 text-xs font-bold transition"
                                            :class="a.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-neutral-100 text-neutral-500 hover:bg-neutral-200'">
                                            {{ a.is_active ? 'Active' : 'Hidden' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <button type="button" @click="openEdit(a)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">Edit</button>
                                        <button type="button" @click="confirmingDelete = a" class="rounded-full px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="!(addons[type] ?? []).length">
                                    <td colspan="6" class="px-6 py-10 text-center text-neutral-400">No {{ label.toLowerCase() }} options yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create / edit modal -->
        <Teleport to="body">
            <div v-if="editing" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4 sm:items-center" @click.self="close">
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-7">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-neutral-900">{{ editing === 'new' ? 'New addon' : 'Edit addon' }}</h3>
                        <button type="button" @click="close" class="rounded-full p-1 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700" aria-label="Close">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form @submit.prevent="save" class="mt-5 space-y-4">
                        <div>
                            <label :class="labelClass">Category</label>
                            <select v-model="form.type" :class="inputClass">
                                <option v-for="(label, key) in types" :key="key" :value="key">{{ label }}</option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.type" />
                        </div>
                        <div>
                            <label :class="labelClass">Name</label>
                            <input v-model="form.name" type="text" :class="inputClass" placeholder="e.g. Premium Gift Box" required />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>
                        <div>
                            <label :class="labelClass">Description</label>
                            <textarea v-model="form.description" rows="2" :class="inputClass"></textarea>
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                        <div>
                            <label :class="labelClass">Preview image <span class="font-normal text-neutral-400">(shown to buyers when choosing this template)</span></label>
                            <div class="flex items-center gap-4">
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-neutral-100 ring-1 ring-neutral-200/70">
                                    <img v-if="imagePreview" :src="imagePreview" alt="" class="h-full w-full object-cover" />
                                    <span v-else class="flex h-full w-full items-center justify-center text-2xl">🎁</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onPickImage" />
                                    <button type="button" @click="fileInput.click()" class="rounded-full bg-neutral-100 px-4 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-200">{{ imagePreview ? 'Change image' : 'Upload image' }}</button>
                                    <button v-if="imagePreview" type="button" @click="removeImage" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Remove image</button>
                                </div>
                            </div>
                            <InputError class="mt-1" :message="form.errors.image" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label :class="labelClass">Price (₹) <span class="font-normal text-neutral-400">(0 = free)</span></label>
                                <input v-model="form.price" type="number" min="0" step="0.01" :class="inputClass" required />
                                <InputError class="mt-1" :message="form.errors.price" />
                            </div>
                            <div>
                                <label :class="labelClass">Sort order</label>
                                <input v-model="form.sort_order" type="number" min="0" :class="inputClass" placeholder="0" />
                                <InputError class="mt-1" :message="form.errors.sort_order" />
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-6">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input v-model="form.is_default" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-neutral-700">Pre-selected by default</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input v-model="form.is_active" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-neutral-700">Visible at checkout</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-neutral-100 pt-5">
                            <button type="button" @click="close" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                                {{ form.processing ? 'Saving…' : editing === 'new' ? 'Add addon' : 'Save changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete confirm -->
        <Teleport to="body">
            <div v-if="confirmingDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="confirmingDelete = null">
                <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">
                    <p class="text-3xl">🗑️</p>
                    <h3 class="mt-3 text-base font-bold text-neutral-900">Delete “{{ confirmingDelete.name }}”?</h3>
                    <p class="mt-2 text-sm text-neutral-500">Past orders keep their own price snapshot and are not affected.</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="confirmingDelete = null" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                        <button type="button" @click="destroy" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">Delete</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
