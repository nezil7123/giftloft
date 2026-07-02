<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
    categories: { type: Object, default: () => ({}) },
    myWishlists: { type: Array, default: null },
    filters: { type: Object, default: () => ({}) },
});

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN')}`;

const accentGrad = {
    indigo: 'from-indigo-500 to-violet-500',
    rose: 'from-rose-400 to-pink-500',
    amber: 'from-amber-400 to-orange-500',
    violet: 'from-violet-500 to-purple-600',
    emerald: 'from-emerald-400 to-teal-500',
    sky: 'from-sky-400 to-cyan-500',
    neutral: 'from-neutral-600 to-neutral-800',
};

// ── Filters ──────────────────────────────────────────────
const activeCategory = ref(props.filters?.category ?? 'all');
const underPrice = ref(props.filters?.max_price === 499);

const filtered = computed(() =>
    props.products.filter((p) => {
        const catOk = activeCategory.value === 'all' || p.category === activeCategory.value;
        const priceOk = !underPrice.value || Number(p.price) <= 499;
        return catOk && priceOk;
    })
);

// ── Add to registry ──────────────────────────────────────
const picker = ref(null); // the product being added, or null
const addForm = useForm({ wishlist_id: null });
const toast = ref('');

const openPicker = (product) => { picker.value = product; };
const closePicker = () => { picker.value = null; };

const addTo = (wishlist) => {
    addForm.wishlist_id = wishlist.id;
    addForm.post(route('registry.products.add', picker.value.slug), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.value = `“${picker.value.name}” added to ${wishlist.name} 🎁`;
            closePicker();
            setTimeout(() => (toast.value = ''), 3800);
        },
    });
};
</script>

<template>
    <PublicLayout theme="light">
        <Head title="Gift Shop — Gift Loft" />

        <!-- Toast -->
        <Transition enter-active-class="transition duration-300" enter-from-class="-translate-y-3 opacity-0" leave-active-class="transition duration-300" leave-to-class="-translate-y-3 opacity-0">
            <div v-if="toast" class="fixed inset-x-0 top-24 z-50 mx-auto w-fit max-w-[90%] rounded-full bg-neutral-900 px-6 py-3 text-sm font-semibold text-white shadow-xl">
                {{ toast }}
            </div>
        </Transition>

        <div class="mx-auto max-w-7xl px-6 pb-16 pt-24 sm:px-8 lg:px-10">
            <!-- Hero -->
            <div class="rounded-[2rem] bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 p-10 text-white shadow-sm sm:p-12">
                <div class="grid gap-8 lg:grid-cols-[1.3fr_0.7fr] lg:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.32em] text-white/70">Gift shop</p>
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Curated gifts, ready for any registry.</h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-white/80">Browse hand-picked gifts and experiences. Add the ones you love straight to your registry — guests can shop them from your event page.</p>
                    </div>
                    <Link :href="myWishlists === null ? '/register' : route('wishlists.index')" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-indigo-700 shadow-sm transition hover:scale-105">
                        {{ myWishlists === null ? 'Create your registry' : 'My registries' }}
                    </Link>
                </div>
            </div>

            <!-- Filter chips -->
            <div class="mt-8 flex flex-wrap items-center gap-2">
                <button type="button" @click="activeCategory = 'all'"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="activeCategory === 'all' ? 'bg-neutral-900 text-white' : 'bg-white text-neutral-600 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                    All gifts
                </button>
                <button v-for="(label, key) in categories" :key="key" type="button" @click="activeCategory = key"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="activeCategory === key ? 'bg-neutral-900 text-white' : 'bg-white text-neutral-600 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                    {{ label }}
                </button>
                <span class="mx-1 hidden h-6 w-px bg-neutral-200 sm:inline-block"></span>
                <button type="button" @click="underPrice = !underPrice"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="underPrice ? 'bg-emerald-600 text-white' : 'bg-white text-neutral-600 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                    Under ₹499
                </button>
            </div>

            <!-- Grid -->
            <div v-if="filtered.length" class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="product in filtered" :key="product.id"
                    class="group flex flex-col overflow-hidden rounded-[1.75rem] border border-neutral-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <!-- Visual -->
                    <Link :href="route('public.shop.show', product.slug)" class="relative block aspect-[4/3] overflow-hidden">
                        <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br text-6xl" :class="accentGrad[product.accent] || accentGrad.indigo">
                            <span class="drop-shadow-sm">{{ product.emoji || '🎁' }}</span>
                        </div>
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-neutral-600 backdrop-blur">{{ categories[product.category] || product.category }}</span>
                    </Link>
                    <!-- Body -->
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-base font-bold text-neutral-900">{{ product.name }}</h2>
                            <span class="shrink-0 text-sm font-bold text-neutral-900">{{ money(product.price) }}</span>
                        </div>
                        <p v-if="product.description" class="mt-1.5 line-clamp-2 text-sm leading-6 text-neutral-500">{{ product.description }}</p>
                        <div class="mt-4 flex items-center gap-2 pt-1">
                            <button type="button" @click="openPicker(product)" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add to registry
                            </button>
                            <Link :href="route('public.shop.show', product.slug)" class="inline-flex items-center justify-center rounded-full px-4 py-2.5 text-sm font-semibold text-neutral-600 ring-1 ring-neutral-200 transition hover:bg-neutral-100">View</Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="mt-8 rounded-[2rem] border border-dashed border-neutral-300 bg-white px-8 py-16 text-center">
                <p class="text-lg font-bold text-neutral-900">No gifts match these filters</p>
                <p class="mt-2 text-sm text-neutral-500">Try a different category or clear the price filter.</p>
            </div>
        </div>

        <!-- Add-to-registry modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-active-class="transition duration-200" leave-to-class="opacity-0">
            <div v-if="picker" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4 sm:items-center" @click.self="closePicker">
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl" :class="accentGrad[picker.accent] || accentGrad.indigo">{{ picker.emoji || '🎁' }}</div>
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-bold text-neutral-900">{{ picker.name }}</h3>
                            <p class="text-sm text-neutral-500">{{ money(picker.price) }}</p>
                        </div>
                        <button type="button" @click="closePicker" class="ml-auto rounded-full p-1 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700" aria-label="Close">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Guest -->
                    <template v-if="myWishlists === null">
                        <p class="mt-6 text-sm text-neutral-600">Sign in to save this gift to one of your registries.</p>
                        <div class="mt-5 flex gap-3">
                            <Link href="/login" class="flex-1 rounded-full bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-indigo-500">Log in</Link>
                            <Link href="/register" class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold text-neutral-700 ring-1 ring-neutral-300 transition hover:bg-neutral-100">Sign up</Link>
                        </div>
                    </template>

                    <!-- No registries yet -->
                    <template v-else-if="!myWishlists.length">
                        <p class="mt-6 text-sm text-neutral-600">You don't have a registry yet. Create one and we'll add this gift to it.</p>
                        <Link :href="route('wishlists.create')" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Create a registry</Link>
                    </template>

                    <!-- Pick a registry -->
                    <template v-else>
                        <p class="mt-6 text-sm font-semibold text-neutral-700">Add to which registry?</p>
                        <div class="mt-3 space-y-2">
                            <button v-for="w in myWishlists" :key="w.id" type="button" :disabled="addForm.processing" @click="addTo(w)"
                                class="flex w-full items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3 text-left text-sm font-semibold text-neutral-800 transition hover:border-indigo-400 hover:bg-indigo-50/50 disabled:opacity-50">
                                <span>{{ w.name }}</span>
                                <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                        <Link :href="route('wishlists.create')" class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">+ New registry</Link>
                    </template>
                </div>
            </div>
        </Transition>
    </PublicLayout>
</template>
