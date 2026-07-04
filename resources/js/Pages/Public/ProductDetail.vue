<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    product: { type: Object, required: true },
    related: { type: Array, default: () => [] },
    categories: { type: Object, default: () => ({}) },
    myWishlists: { type: Array, default: null },
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

const showPicker = ref(false);
const addForm = useForm({ wishlist_id: null });
const toast = ref('');

const addTo = (wishlist) => {
    addForm.wishlist_id = wishlist.id;
    addForm.post(route('registry.products.add', props.product.slug), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.value = `Added to ${wishlist.name} 🎁`;
            showPicker.value = false;
            setTimeout(() => (toast.value = ''), 3800);
        },
    });
};

// ── Add to cart ───────────────────────────────────────────
const cartForm = useForm({});
const justAdded = ref(false);
const addToCart = () => {
    cartForm.post(route('cart.add', props.product.slug), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.value = `“${props.product.name}” added to cart 🛍️`;
            justAdded.value = true;
            setTimeout(() => (toast.value = ''), 3800);
            setTimeout(() => (justAdded.value = false), 1800);
        },
    });
};
</script>

<template>
    <PublicLayout theme="light">
        <Head :title="`${product.name} — Gift Loft`" />

        <Transition enter-active-class="transition duration-300" enter-from-class="-translate-y-3 opacity-0" leave-active-class="transition duration-300" leave-to-class="-translate-y-3 opacity-0">
            <div v-if="toast" class="fixed inset-x-0 top-24 z-50 mx-auto w-fit rounded-full bg-neutral-900 px-6 py-3 text-sm font-semibold text-white shadow-xl">{{ toast }}</div>
        </Transition>

        <div class="mx-auto max-w-6xl px-6 pb-16 pt-24 sm:px-8 lg:px-10">
            <Link :href="route('public.shop')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to shop</Link>

            <div class="mt-5 grid gap-8 lg:grid-cols-2 lg:items-start">
                <!-- Visual -->
                <div class="overflow-hidden rounded-[2rem] border border-neutral-200 bg-white shadow-sm">
                    <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="aspect-square w-full object-cover" />
                    <div v-else class="flex aspect-square w-full items-center justify-center bg-gradient-to-br text-[10rem]" :class="accentGrad[product.accent] || accentGrad.indigo">
                        <span class="drop-shadow">{{ product.emoji || '🎁' }}</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="lg:py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-600">{{ categories[product.category] || product.category }}</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl">{{ product.name }}</h1>
                    <p class="mt-4 text-2xl font-bold text-neutral-900">{{ money(product.price) }}</p>
                    <p v-if="product.description" class="mt-5 text-base leading-7 text-neutral-600">{{ product.description }}</p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button type="button" @click="addToCart" :disabled="cartForm.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-indigo-600 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-70">
                            <svg v-if="cartForm.processing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            <svg v-else-if="justAdded" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m-10 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z" /></svg>
                            {{ cartForm.processing ? 'Adding…' : justAdded ? 'Added' : 'Add to cart' }}
                        </button>
                        <button type="button" @click="showPicker = true" class="inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-semibold text-neutral-700 ring-1 ring-neutral-300 transition hover:bg-neutral-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add to wishlist
                        </button>
                        <a v-if="product.product_url" :href="product.product_url" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full px-7 py-3.5 text-sm font-semibold text-neutral-700 ring-1 ring-neutral-300 transition hover:bg-neutral-100">View product ↗</a>
                    </div>
                </div>
            </div>

            <!-- Related -->
            <div v-if="related.length" class="mt-16">
                <h2 class="text-lg font-bold text-neutral-900">More {{ (categories[product.category] || '').toLowerCase() || 'gifts' }}</h2>
                <div class="mt-5 grid gap-6 sm:grid-cols-3">
                    <Link v-for="r in related" :key="r.id" :href="route('public.shop.show', r.slug)"
                        class="group overflow-hidden rounded-[1.5rem] border border-neutral-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img v-if="r.image_url" :src="r.image_url" :alt="r.name" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br text-5xl" :class="accentGrad[r.accent] || accentGrad.indigo">{{ r.emoji || '🎁' }}</div>
                        </div>
                        <div class="flex items-center justify-between p-4">
                            <span class="text-sm font-bold text-neutral-900">{{ r.name }}</span>
                            <span class="text-sm font-semibold text-neutral-600">{{ money(r.price) }}</span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Add-to-wishlist modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-active-class="transition duration-200" leave-to-class="opacity-0">
            <div v-if="showPicker" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4 sm:items-center" @click.self="showPicker = false">
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-2xl" :class="accentGrad[product.accent] || accentGrad.indigo">{{ product.emoji || '🎁' }}</div>
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-bold text-neutral-900">{{ product.name }}</h3>
                            <p class="text-sm text-neutral-500">{{ money(product.price) }}</p>
                        </div>
                        <button type="button" @click="showPicker = false" class="ml-auto rounded-full p-1 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700" aria-label="Close">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <template v-if="myWishlists === null">
                        <p class="mt-6 text-sm text-neutral-600">Sign in to save this gift to one of your wishlists.</p>
                        <div class="mt-5 flex gap-3">
                            <Link href="/login" class="flex-1 rounded-full bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-indigo-500">Log in</Link>
                            <Link href="/register" class="flex-1 rounded-full px-4 py-2.5 text-center text-sm font-semibold text-neutral-700 ring-1 ring-neutral-300 transition hover:bg-neutral-100">Sign up</Link>
                        </div>
                    </template>

                    <template v-else-if="!myWishlists.length">
                        <p class="mt-6 text-sm text-neutral-600">You don't have a wishlist yet. Create one and we'll add this gift to it.</p>
                        <Link :href="route('wishlists.create')" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Create a wishlist</Link>
                    </template>

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
