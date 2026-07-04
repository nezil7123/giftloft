<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
    subtotal: { type: Number, default: 0 },
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

const setQty = (product, quantity) => {
    router.put(route('cart.update', product.slug), { quantity }, { preserveScroll: true });
};

const remove = (product) => {
    router.delete(route('cart.remove', product.slug), { preserveScroll: true });
};
</script>

<template>
    <PublicLayout theme="light">
        <Head title="Your Cart — Gift Loft" />

        <div class="mx-auto max-w-5xl px-6 pb-20 pt-24 sm:px-8 lg:px-10">
            <h1 class="text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl">Your cart</h1>
            <p class="mt-2 text-sm text-neutral-500">Review your gifts, then check out — buy for yourself or send it as a personal gift.</p>

            <div v-if="items.length" class="mt-8 grid gap-8 lg:grid-cols-[1.6fr_1fr] lg:items-start">
                <!-- Line items -->
                <div class="divide-y divide-neutral-100 rounded-[1.75rem] border border-neutral-200 bg-white shadow-sm">
                    <div v-for="row in items" :key="row.product.id" class="flex gap-4 p-5">
                        <Link :href="route('public.shop.show', row.product.slug)" class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-neutral-100">
                            <img v-if="row.product.image_url" :src="row.product.image_url" :alt="row.product.name" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br text-3xl" :class="accentGrad[row.product.accent] || accentGrad.indigo">
                                {{ row.product.emoji || '🎁' }}
                            </div>
                        </Link>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <Link :href="route('public.shop.show', row.product.slug)" class="truncate text-sm font-bold text-neutral-900 hover:text-indigo-600">{{ row.product.name }}</Link>
                                <span class="shrink-0 text-sm font-bold text-neutral-900">{{ money(row.line_total) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-neutral-500">{{ money(row.product.price) }} each</p>
                            <div class="mt-3 flex items-center gap-3">
                                <div class="flex items-center rounded-full border border-neutral-200">
                                    <button type="button" @click="setQty(row.product, row.quantity - 1)" class="flex h-8 w-8 items-center justify-center text-neutral-500 transition hover:text-neutral-900" aria-label="Decrease quantity">−</button>
                                    <span class="w-7 text-center text-sm font-semibold text-neutral-800 tabular-nums">{{ row.quantity }}</span>
                                    <button type="button" @click="setQty(row.product, row.quantity + 1)" class="flex h-8 w-8 items-center justify-center text-neutral-500 transition hover:text-neutral-900" aria-label="Increase quantity">+</button>
                                </div>
                                <button type="button" @click="remove(row.product)" class="text-xs font-semibold text-neutral-400 transition hover:text-rose-600">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="rounded-[1.75rem] border border-neutral-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-bold text-neutral-900">Order summary</h2>
                    <div class="mt-4 space-y-2 border-t border-neutral-100 pt-4 text-sm">
                        <div class="flex justify-between text-neutral-600"><span>Subtotal</span><span>{{ money(subtotal) }}</span></div>
                        <div class="flex justify-between text-base font-bold text-neutral-900"><span>Total</span><span>{{ money(subtotal) }}</span></div>
                    </div>
                    <Link :href="route('checkout.cart')" class="mt-6 flex w-full items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Checkout
                    </Link>
                    <Link :href="route('public.shop')" class="mt-3 block text-center text-xs font-medium text-neutral-400 hover:text-neutral-600">Continue shopping</Link>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="mt-8 rounded-[2rem] border border-dashed border-neutral-300 bg-white px-8 py-16 text-center">
                <p class="text-lg font-bold text-neutral-900">Your cart is empty</p>
                <p class="mt-2 text-sm text-neutral-500">Browse the shop and add gifts you love.</p>
                <Link :href="route('public.shop')" class="mt-5 inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">Browse the shop</Link>
            </div>
        </div>
    </PublicLayout>
</template>
