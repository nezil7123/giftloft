<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
    categories: { type: Object, default: () => ({}) },
    genders: { type: Object, default: () => ({}) },
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
const activeGender = ref('all');

const priceBuckets = [
    { key: 'all', label: 'Any price', min: null, max: null },
    { key: 'b0', label: '₹0 – 199', min: 0, max: 199 },
    { key: 'b1', label: '₹200 – 499', min: 200, max: 499 },
    { key: 'b2', label: '₹500 – 999', min: 500, max: 999 },
    { key: 'b3', label: '₹1000+', min: 1000, max: null },
];
const activePrice = ref('all');
// Legacy "Under ₹X" deep links (e.g. from the nav) fall outside the fixed
// buckets below — honoured once on load, then cleared as soon as the
// visitor picks a bucket themselves.
const legacyMaxPrice = ref(props.filters?.max_price ?? null);

const sortOptions = [
    { key: 'featured', label: 'Featured' },
    { key: 'price_asc', label: 'Price: Low to High' },
    { key: 'price_desc', label: 'Price: High to Low' },
    { key: 'name_asc', label: 'Name: A–Z' },
];
const sortKey = ref('featured');

const hasActiveFilters = computed(() => activeCategory.value !== 'all' || activeGender.value !== 'all' || activePrice.value !== 'all' || legacyMaxPrice.value !== null);
const clearFilters = () => {
    activeCategory.value = 'all';
    activeGender.value = 'all';
    activePrice.value = 'all';
    legacyMaxPrice.value = null;
};
const selectPriceBucket = (key) => {
    activePrice.value = key;
    legacyMaxPrice.value = null;
};

const filtered = computed(() => {
    const bucket = priceBuckets.find((b) => b.key === activePrice.value);

    let list = props.products.filter((p) => {
        const catOk = activeCategory.value === 'all' || p.category === activeCategory.value;
        const genderOk = activeGender.value === 'all' || !p.gender || p.gender === 'unisex' || p.gender === activeGender.value;
        const price = Number(p.price);
        const priceOk = bucket && bucket.key !== 'all'
            ? ((bucket.min === null || price >= bucket.min) && (bucket.max === null || price <= bucket.max))
            : (legacyMaxPrice.value === null || price <= legacyMaxPrice.value);
        return catOk && genderOk && priceOk;
    });

    if (sortKey.value === 'price_asc') list = [...list].sort((a, b) => Number(a.price) - Number(b.price));
    else if (sortKey.value === 'price_desc') list = [...list].sort((a, b) => Number(b.price) - Number(a.price));
    else if (sortKey.value === 'name_asc') list = [...list].sort((a, b) => a.name.localeCompare(b.name));

    return list;
});

// ── Add to wishlist ──────────────────────────────────────
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

// ── Add to cart ───────────────────────────────────────────
const cartForm = useForm({});
const addingId = ref(null); // product currently being added
const addedId = ref(null); // product that just finished, briefly shows a checkmark

const addToCart = (product) => {
    addingId.value = product.id;
    cartForm.post(route('cart.add', product.slug), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.value = `“${product.name}” added to cart 🛍️`;
            addedId.value = product.id;
            setTimeout(() => (toast.value = ''), 3800);
            setTimeout(() => { if (addedId.value === product.id) addedId.value = null; }, 1800);
        },
        onFinish: () => {
            if (addingId.value === product.id) addingId.value = null;
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
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Curated gifts, ready for any wishlist.</h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-white/80">Browse hand-picked gifts and experiences. Add the ones you love straight to your wishlist — guests can shop them from your event page.</p>
                    </div>
                    <Link :href="myWishlists === null ? '/register' : route('wishlists.index')" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-indigo-700 shadow-sm transition hover:scale-105">
                        {{ myWishlists === null ? 'Create your wishlist' : 'My wishlists' }}
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-8 space-y-3">
                <!-- Category -->
                <div class="flex flex-wrap items-center gap-2">
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
                </div>

                <!-- Gender + price + sort -->
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="activeGender = 'all'"
                            class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition"
                            :class="activeGender === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-neutral-500 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                            Everyone
                        </button>
                        <button v-for="(label, key) in genders" :key="key" type="button" @click="activeGender = key"
                            class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition"
                            :class="activeGender === key ? 'bg-indigo-600 text-white' : 'bg-white text-neutral-500 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                            {{ label }}
                        </button>
                    </div>

                    <span class="hidden h-5 w-px bg-neutral-200 sm:inline-block"></span>

                    <div class="flex flex-wrap items-center gap-2">
                        <button v-for="bucket in priceBuckets" :key="bucket.key" type="button" @click="selectPriceBucket(bucket.key)"
                            class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition"
                            :class="activePrice === bucket.key ? 'bg-emerald-600 text-white' : 'bg-white text-neutral-500 ring-1 ring-neutral-200 hover:bg-neutral-100'">
                            {{ bucket.label }}
                        </button>
                    </div>

                    <button v-if="hasActiveFilters" type="button" @click="clearFilters" class="text-xs font-semibold text-neutral-400 underline-offset-2 transition hover:text-neutral-700 hover:underline">
                        Clear filters
                    </button>

                    <div class="ml-auto flex items-center gap-2">
                        <label class="text-xs font-medium text-neutral-400">{{ filtered.length }} gift{{ filtered.length === 1 ? '' : 's' }}</label>
                        <select v-model="sortKey" class="rounded-full border-neutral-200 bg-white py-1.5 pl-3 pr-8 text-xs font-semibold text-neutral-700 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500">
                            <option v-for="opt in sortOptions" :key="opt.key" :value="opt.key">Sort: {{ opt.label }}</option>
                        </select>
                    </div>
                </div>
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
                        <div class="mt-4 flex flex-col gap-2 pt-1">
                            <button type="button" @click="addToCart(product)" :disabled="addingId === product.id"
                                class="inline-flex items-center justify-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-70">
                                <svg v-if="addingId === product.id" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                <svg v-else-if="addedId === product.id" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-10 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z" /></svg>
                                {{ addingId === product.id ? 'Adding…' : addedId === product.id ? 'Added' : 'Add to cart' }}
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="openPicker(product)" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-full px-4 py-2 text-xs font-semibold text-neutral-600 ring-1 ring-neutral-200 transition hover:bg-neutral-100">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Wishlist
                                </button>
                                <Link :href="route('public.shop.show', product.slug)" class="inline-flex flex-1 items-center justify-center rounded-full px-4 py-2 text-xs font-semibold text-neutral-600 ring-1 ring-neutral-200 transition hover:bg-neutral-100">View</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="mt-8 rounded-[2rem] border border-dashed border-neutral-300 bg-white px-8 py-16 text-center">
                <p class="text-lg font-bold text-neutral-900">No gifts match these filters</p>
                <p class="mt-2 text-sm text-neutral-500">Try a different category, audience, or price range.</p>
                <button type="button" @click="clearFilters" class="mt-5 inline-flex items-center justify-center rounded-full bg-neutral-900 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-neutral-800">Clear filters</button>
            </div>
        </div>

        <!-- Add-to-wishlist modal -->
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
                        <p class="mt-6 text-sm text-neutral-600">Sign in to save this gift to one of your wishlists.</p>
                        <div class="mt-5 flex gap-3">
                            <Link href="/login" class="flex-1 rounded-full bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-indigo-500">Log in</Link>
                            <Link href="/register" class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold text-neutral-700 ring-1 ring-neutral-300 transition hover:bg-neutral-100">Sign up</Link>
                        </div>
                    </template>

                    <!-- No wishlists yet -->
                    <template v-else-if="!myWishlists.length">
                        <p class="mt-6 text-sm text-neutral-600">You don't have a wishlist yet. Create one and we'll add this gift to it.</p>
                        <Link :href="route('wishlists.create')" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Create a wishlist</Link>
                    </template>

                    <!-- Pick a wishlist -->
                    <template v-else>
                        <p class="mt-6 text-sm font-semibold text-neutral-700">Add to which wishlist?</p>
                        <div class="mt-3 space-y-2">
                            <button v-for="w in myWishlists" :key="w.id" type="button" :disabled="addForm.processing" @click="addTo(w)"
                                class="flex w-full items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3 text-left text-sm font-semibold text-neutral-800 transition hover:border-indigo-400 hover:bg-indigo-50/50 disabled:opacity-50">
                                <span>{{ w.name }}</span>
                                <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                        <Link :href="route('wishlists.create')" class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">+ New wishlist</Link>
                    </template>
                </div>
            </div>
        </Transition>
    </PublicLayout>
</template>
