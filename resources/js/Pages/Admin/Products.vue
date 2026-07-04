<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import Pager from './Partials/Pager.vue';
import InputError from '@/Components/InputError.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: { type: Object, required: true }, // paginator
    categories: { type: Object, required: true },
    genders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;

const accents = ['indigo', 'rose', 'amber', 'violet', 'emerald', 'sky', 'neutral'];
const accentBg = {
    indigo: 'bg-indigo-500', rose: 'bg-rose-500', amber: 'bg-amber-500',
    violet: 'bg-violet-500', emerald: 'bg-emerald-500', sky: 'bg-sky-500', neutral: 'bg-neutral-600',
};

// ── Search ──
const q = ref(props.filters?.q ?? '');
const search = () => router.get(route('admin.products.index'), q.value ? { q: q.value } : {}, { preserveState: true, preserveScroll: true });

// ── Create / edit modal ──
const editing = ref(null); // null = closed, 'new' = create, product object = edit
const form = useForm({
    name: '', category: 'wedding', gender: 'unisex', price: '', description: '',
    image_url: '', product_url: '', emoji: '🎁', accent: 'indigo',
    is_active: true, sort_order: null,
});

const openCreate = () => {
    form.reset();
    form.clearErrors();
    editing.value = 'new';
};
const openEdit = (p) => {
    form.clearErrors();
    Object.assign(form, {
        name: p.name, category: p.category, gender: p.gender ?? 'unisex', price: p.price, description: p.description ?? '',
        image_url: p.image_url ?? '', product_url: p.product_url ?? '', emoji: p.emoji ?? '🎁',
        accent: p.accent ?? 'indigo', is_active: !!p.is_active, sort_order: p.sort_order,
    });
    editing.value = p;
};
const close = () => (editing.value = null);

const save = () => {
    const opts = { preserveScroll: true, onSuccess: close };
    if (editing.value === 'new') form.post(route('admin.products.store'), opts);
    else form.put(route('admin.products.update', editing.value.slug), opts);
};

const toggleActive = (p) => {
    router.put(route('admin.products.update', p.slug), { ...p, is_active: !p.is_active }, { preserveScroll: true });
};

const confirmingDelete = ref(null);
const destroy = () => {
    router.delete(route('admin.products.destroy', confirmingDelete.value.slug), {
        preserveScroll: true,
        onSuccess: () => (confirmingDelete.value = null),
    });
};

const inputClass = 'block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500';
const labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
</script>

<template>
    <Head title="Admin — Products" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Shop catalog</h2>
                <p class="text-sm text-neutral-500">Add, edit and retire the products guests can gift.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <!-- Toolbar -->
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <form @submit.prevent="search" class="relative w-full sm:max-w-xs">
                        <input v-model="q" type="search" placeholder="Search products…" :class="inputClass" @change="search" />
                    </form>
                    <button type="button" @click="openCreate" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        New product
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[720px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">Product</th>
                                <th class="px-4 py-4 font-semibold">Category</th>
                                <th class="px-4 py-4 font-semibold">Gender</th>
                                <th class="px-4 py-4 font-semibold">Price</th>
                                <th class="px-4 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="p in products.data" :key="p.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl text-lg text-white" :class="accentBg[p.accent] || 'bg-neutral-400'">
                                            <img v-if="p.image_url" :src="p.image_url" class="h-full w-full object-cover" />
                                            <span v-else>{{ p.emoji || '🎁' }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-neutral-900">{{ p.name }}</p>
                                            <p class="truncate text-xs text-neutral-400">/shop/{{ p.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-600">{{ categories[p.category] ?? p.category }}</td>
                                <td class="px-4 py-3.5 text-neutral-500">{{ genders[p.gender] ?? '—' }}</td>
                                <td class="px-4 py-3.5 font-semibold tabular-nums text-neutral-900">{{ money(p.price) }}</td>
                                <td class="px-4 py-3.5">
                                    <button type="button" @click="toggleActive(p)"
                                        class="rounded-full px-3 py-1 text-xs font-bold transition"
                                        :class="p.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-neutral-100 text-neutral-500 hover:bg-neutral-200'">
                                        {{ p.is_active ? 'Active' : 'Hidden' }}
                                    </button>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <button type="button" @click="openEdit(p)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">Edit</button>
                                    <button type="button" @click="confirmingDelete = p" class="rounded-full px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="!products.data.length">
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-400">No products found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pager :paginator="products" />
            </div>
        </div>

        <!-- Create / edit modal -->
        <Teleport to="body">
            <div v-if="editing" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4 sm:items-center" @click.self="close">
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-7">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-neutral-900">{{ editing === 'new' ? 'New product' : 'Edit product' }}</h3>
                        <button type="button" @click="close" class="rounded-full p-1 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700" aria-label="Close">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form @submit.prevent="save" class="mt-5 space-y-4">
                        <div>
                            <label :class="labelClass">Name</label>
                            <input v-model="form.name" type="text" :class="inputClass" placeholder="e.g. Marble Cheese Board" required />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label :class="labelClass">Category</label>
                                <select v-model="form.category" :class="inputClass">
                                    <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.category" />
                            </div>
                            <div>
                                <label :class="labelClass">Gender</label>
                                <select v-model="form.gender" :class="inputClass">
                                    <option v-for="(label, key) in genders" :key="key" :value="key">{{ label }}</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.gender" />
                            </div>
                        </div>
                        <div>
                            <label :class="labelClass">Price (₹)</label>
                            <input v-model="form.price" type="number" min="0" step="0.01" :class="inputClass" required />
                            <InputError class="mt-1" :message="form.errors.price" />
                        </div>
                        <div>
                            <label :class="labelClass">Description</label>
                            <textarea v-model="form.description" rows="2" :class="inputClass"></textarea>
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label :class="labelClass">Emoji (card art)</label>
                                <input v-model="form.emoji" type="text" :class="inputClass" placeholder="🎁" />
                                <InputError class="mt-1" :message="form.errors.emoji" />
                            </div>
                            <div>
                                <label :class="labelClass">Accent</label>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <button v-for="a in accents" :key="a" type="button" @click="form.accent = a"
                                        class="h-8 w-8 rounded-full ring-2 ring-offset-2 transition" :class="[accentBg[a], form.accent === a ? 'ring-neutral-900' : 'ring-transparent hover:scale-110']"
                                        :aria-label="a"></button>
                                </div>
                                <InputError class="mt-1" :message="form.errors.accent" />
                            </div>
                        </div>
                        <div>
                            <label :class="labelClass">Image URL <span class="font-normal text-neutral-400">(optional — overrides emoji)</span></label>
                            <input v-model="form.image_url" type="url" :class="inputClass" placeholder="https://…" />
                            <InputError class="mt-1" :message="form.errors.image_url" />
                        </div>
                        <div>
                            <label :class="labelClass">External product URL <span class="font-normal text-neutral-400">(optional)</span></label>
                            <input v-model="form.product_url" type="url" :class="inputClass" placeholder="https://…" />
                            <InputError class="mt-1" :message="form.errors.product_url" />
                        </div>
                        <label class="flex w-fit cursor-pointer items-center gap-2.5">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-sm text-neutral-700">Visible in the shop</span>
                        </label>

                        <div class="flex justify-end gap-3 border-t border-neutral-100 pt-5">
                            <button type="button" @click="close" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                            <button type="submit" :disabled="form.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                                {{ form.processing ? 'Saving…' : editing === 'new' ? 'Add product' : 'Save changes' }}
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
                    <p class="mt-2 text-sm text-neutral-500">This removes it from the shop permanently. Wishlist items already added are not affected.</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="confirmingDelete = null" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                        <button type="button" @click="destroy" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">Delete</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
