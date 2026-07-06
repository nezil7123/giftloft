<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    wishlist: { type: Object, required: true },
    catalog: { type: Array, default: () => [] },
    categories: { type: Object, default: () => ({}) },
});

const inputClass = 'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

const shareUrl = computed(() =>
    props.wishlist.visibility !== 'private' && props.wishlist.slug
        ? `${window.location.origin}/r/${props.wishlist.slug}`
        : null
);
const copied = ref(false);
const copyShare = async () => {
    if (!shareUrl.value) return;
    await navigator.clipboard.writeText(shareUrl.value);
    copied.value = true;
    setTimeout(() => (copied.value = false), 1800);
};

const money = (v) => (v == null ? null : `₹${Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);

// ── Add item: catalog picker ──────────────────────────────
const showPicker = ref(false);
const search = ref('');
const activeCategory = ref('all');
const addingId = ref(null);
const addForm = useForm({ wishlist_id: props.wishlist.id });

// Product ids already on this wishlist, so we can badge them in the picker.
const addedIds = computed(() => new Set(props.wishlist.items.map((i) => i.product_id).filter(Boolean)));

const filteredCatalog = computed(() => {
    const q = search.value.trim().toLowerCase();
    return props.catalog.filter((p) => {
        if (activeCategory.value !== 'all' && p.category !== activeCategory.value) return false;
        if (q && !p.name.toLowerCase().includes(q)) return false;
        return true;
    });
});

const openPicker = () => {
    search.value = '';
    activeCategory.value = 'all';
    showPicker.value = true;
};

const addProduct = (product) => {
    addingId.value = product.id;
    addForm.post(route('registry.products.add', product.slug), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { if (addingId.value === product.id) addingId.value = null; },
    });
};

// ── Edit item: owner-editable fields only ─────────────────
const showEdit = ref(false);
const editingId = ref(null);
const editForm = useForm({ quantity: 1, note: '', is_giftable: true });

const openEdit = (item) => {
    editingId.value = item.id;
    editForm.clearErrors();
    editForm.quantity = item.quantity ?? 1;
    editForm.note = item.note ?? '';
    editForm.is_giftable = Boolean(item.is_giftable);
    showEdit.value = true;
};

const submitEdit = () => {
    editForm.put(route('wishlists.items.update', [props.wishlist.id, editingId.value]), {
        preserveScroll: true,
        onSuccess: () => { showEdit.value = false; editingId.value = null; },
    });
};

const deleteItem = (item) => {
    if (confirm(`Remove "${item.title}" from this wishlist?`)) {
        router.delete(route('wishlists.items.destroy', [props.wishlist.id, item.id]), { preserveScroll: true });
    }
};
</script>

<template>
    <Head :title="wishlist.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Link :href="route('wishlists.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← All wishlists</Link>
                    <h2 class="mt-1 text-xl font-bold text-neutral-900">{{ wishlist.name }}</h2>
                </div>
                <Link :href="route('wishlists.edit', wishlist.id)" class="rounded-full bg-neutral-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700">Edit</Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Meta -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-indigo-700">{{ wishlist.visibility }}</span>
                        <span v-if="!wishlist.active" class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-neutral-500">Inactive</span>
                        <Link v-if="wishlist.event" :href="route('events.show', wishlist.event.id)" class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600">↪ {{ wishlist.event.title }}</Link>
                    </div>
                    <p v-if="wishlist.description" class="mt-4 text-sm leading-7 text-neutral-700">{{ wishlist.description }}</p>

                    <div v-if="shareUrl" class="mt-5 flex flex-col gap-2 border-t border-neutral-100 pt-5 sm:flex-row">
                        <input :value="shareUrl" readonly class="w-full rounded-xl border-neutral-300 bg-neutral-50 text-sm text-neutral-600" />
                        <button @click="copyShare" class="shrink-0 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">{{ copied ? 'Copied!' : 'Copy link' }}</button>
                    </div>
                </div>

                <!-- Items -->
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-neutral-900">Items <span class="text-neutral-400">({{ wishlist.items.length }})</span></h3>
                    <button @click="openPicker" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add item
                    </button>
                </div>

                <div v-if="wishlist.items.length" class="grid gap-4 sm:grid-cols-2">
                    <div v-for="item in wishlist.items" :key="item.id" class="flex gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-neutral-200/70">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-neutral-100">
                            <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-neutral-300">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12M3.75 7.5h16.5a1.5 1.5 0 011.5 1.5v.75H2.25V9a1.5 1.5 0 011.5-1.5z" /></svg>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="truncate text-sm font-bold text-neutral-900">{{ item.title }}</h4>
                                <span v-if="money(item.price)" class="shrink-0 text-sm font-semibold text-neutral-700">{{ money(item.price) }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-neutral-500">Qty {{ item.quantity }} · {{ item.claimed_quantity }} claimed</p>
                            <p v-if="item.note" class="mt-1 line-clamp-1 text-xs text-neutral-400">{{ item.note }}</p>
                            <div class="mt-2 flex items-center gap-3 text-xs">
                                <a v-if="item.product_url" :href="item.product_url" target="_blank" rel="noopener" class="font-semibold text-indigo-600 hover:underline">View product</a>
                                <button @click="openEdit(item)" class="font-semibold text-neutral-500 hover:text-neutral-900">Edit</button>
                                <button @click="deleteItem(item)" class="font-semibold text-rose-500 hover:text-rose-700">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                    <p class="text-base font-bold text-neutral-900">No items yet</p>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-neutral-500">Add gifts you'd love to receive from our curated shop.</p>
                    <button @click="openPicker" class="mt-5 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Browse the shop</button>
                </div>
            </div>
        </div>

        <!-- Add item: catalog picker -->
        <Modal :show="showPicker" @close="showPicker = false" max-width="2xl">
            <div class="flex h-[80vh] flex-col">
                <div class="border-b border-neutral-200 p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-neutral-900">Add from the shop</h3>
                        <button @click="showPicker = false" class="rounded-full p-1.5 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <input v-model="search" type="search" placeholder="Search products…" class="mt-4 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="activeCategory = 'all'"
                            :class="['rounded-full px-3 py-1 text-xs font-semibold transition', activeCategory === 'all' ? 'bg-neutral-900 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200']">
                            All
                        </button>
                        <button
                            v-for="(label, key) in categories"
                            :key="key"
                            type="button"
                            @click="activeCategory = key"
                            :class="['rounded-full px-3 py-1 text-xs font-semibold transition', activeCategory === key ? 'bg-neutral-900 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200']">
                            {{ label }}
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <div v-if="filteredCatalog.length" class="grid gap-3 sm:grid-cols-2">
                        <div v-for="product in filteredCatalog" :key="product.id" class="flex items-center gap-3 rounded-2xl border border-neutral-200 p-3">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-neutral-100 text-2xl">
                                <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="h-full w-full object-cover" />
                                <span v-else>{{ product.emoji || '🎁' }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-sm font-bold text-neutral-900">{{ product.name }}</h4>
                                <p class="text-xs font-semibold text-neutral-500">{{ money(product.price) }}</p>
                            </div>
                            <button
                                type="button"
                                @click="addProduct(product)"
                                :disabled="addingId === product.id"
                                class="shrink-0 rounded-full px-3 py-1.5 text-xs font-bold transition disabled:opacity-50"
                                :class="addedIds.has(product.id) ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-indigo-600 text-white hover:bg-indigo-500'">
                                {{ addingId === product.id ? '…' : (addedIds.has(product.id) ? '✓ Add again' : 'Add') }}
                            </button>
                        </div>
                    </div>
                    <div v-else class="flex h-full flex-col items-center justify-center text-center">
                        <p class="text-sm font-semibold text-neutral-700">No products match</p>
                        <p class="mt-1 text-xs text-neutral-500">Try a different search or category.</p>
                    </div>
                </div>

                <div class="border-t border-neutral-200 p-4 text-center">
                    <Link :href="route('public.shop')" class="text-sm font-semibold text-indigo-600 hover:underline">Open the full shop →</Link>
                </div>
            </div>
        </Modal>

        <!-- Edit item: quantity / note / giftability -->
        <Modal :show="showEdit" @close="showEdit = false" max-width="lg">
            <form @submit.prevent="submitEdit" class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-neutral-900">Edit item</h3>
                <p class="mt-1 text-sm text-neutral-500">Product details come from the shop. You can adjust quantity and your note.</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Quantity <span class="text-rose-500">*</span></label>
                        <input v-model="editForm.quantity" type="number" min="1" :class="inputClass" />
                        <InputError class="mt-1.5" :message="editForm.errors.quantity" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Note</label>
                        <textarea v-model="editForm.note" rows="2" :class="inputClass" placeholder="Colour, size, preference…"></textarea>
                        <InputError class="mt-1.5" :message="editForm.errors.note" />
                    </div>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-neutral-300 px-4 py-2.5">
                        <input v-model="editForm.is_giftable" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm text-neutral-700">Guests can gift this item</span>
                    </label>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-neutral-200 pt-6">
                    <button type="button" @click="showEdit = false" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                        {{ editForm.processing ? 'Saving…' : 'Save item' }}
                    </button>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
