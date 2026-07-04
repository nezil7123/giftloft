<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    wishlist: { type: Object, required: true },
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

// ── Item modal ──
const showItemModal = ref(false);
const editingId = ref(null);

const itemForm = useForm({
    title: '', product_url: '', image_url: '', price: '', quantity: 1, note: '', is_giftable: true,
});

const openAdd = () => {
    editingId.value = null;
    itemForm.reset();
    itemForm.clearErrors();
    showItemModal.value = true;
};

const openEdit = (item) => {
    editingId.value = item.id;
    itemForm.clearErrors();
    itemForm.title = item.title;
    itemForm.product_url = item.product_url ?? '';
    itemForm.image_url = item.image_url ?? '';
    itemForm.price = item.price ?? '';
    itemForm.quantity = item.quantity ?? 1;
    itemForm.note = item.note ?? '';
    itemForm.is_giftable = Boolean(item.is_giftable);
    showItemModal.value = true;
};

const submitItem = () => {
    const opts = {
        preserveScroll: true,
        onSuccess: () => { showItemModal.value = false; itemForm.reset(); editingId.value = null; },
    };
    if (editingId.value) {
        itemForm.put(route('wishlists.items.update', [props.wishlist.id, editingId.value]), opts);
    } else {
        itemForm.post(route('wishlists.items.store', props.wishlist.id), opts);
    }
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
                    <button @click="openAdd" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
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
                    <p class="mx-auto mt-2 max-w-sm text-sm text-neutral-500">Add the gifts you'd love to receive — products, experiences, or cash funds.</p>
                    <button @click="openAdd" class="mt-5 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Add your first item</button>
                </div>
            </div>
        </div>

        <!-- Item add/edit modal -->
        <Modal :show="showItemModal" @close="showItemModal = false" max-width="lg">
            <form @submit.prevent="submitItem" class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-neutral-900">{{ editingId ? 'Edit item' : 'Add item' }}</h3>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Title <span class="text-rose-500">*</span></label>
                        <input v-model="itemForm.title" type="text" :class="inputClass" placeholder="e.g. Espresso machine" autofocus />
                        <InputError class="mt-1.5" :message="itemForm.errors.title" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Price (₹)</label>
                            <input v-model="itemForm.price" type="number" step="0.01" min="0" :class="inputClass" placeholder="0.00" />
                            <InputError class="mt-1.5" :message="itemForm.errors.price" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Quantity <span class="text-rose-500">*</span></label>
                            <input v-model="itemForm.quantity" type="number" min="1" :class="inputClass" />
                            <InputError class="mt-1.5" :message="itemForm.errors.quantity" />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Product URL</label>
                        <input v-model="itemForm.product_url" type="url" :class="inputClass" placeholder="https://…" />
                        <InputError class="mt-1.5" :message="itemForm.errors.product_url" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Image URL</label>
                        <input v-model="itemForm.image_url" type="url" :class="inputClass" placeholder="https://…" />
                        <InputError class="mt-1.5" :message="itemForm.errors.image_url" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-800">Note</label>
                        <textarea v-model="itemForm.note" rows="2" :class="inputClass" placeholder="Colour, size, preference…"></textarea>
                        <InputError class="mt-1.5" :message="itemForm.errors.note" />
                    </div>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-neutral-300 px-4 py-2.5">
                        <input v-model="itemForm.is_giftable" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm text-neutral-700">Guests can gift this item</span>
                    </label>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-neutral-200 pt-6">
                    <button type="button" @click="showItemModal = false" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                    <button type="submit" :disabled="itemForm.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                        {{ itemForm.processing ? 'Saving…' : (editingId ? 'Save item' : 'Add item') }}
                    </button>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
