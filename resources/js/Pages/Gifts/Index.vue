<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    sent: { type: Array, default: () => [] },
    received: { type: Array, default: () => [] },
});

const tab = ref('sent');
const list = computed(() => (tab.value === 'sent' ? props.sent : props.received));

const money = (v) => `₹${Number(v ?? 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '');

const statusBadge = {
    completed: 'bg-emerald-100 text-emerald-700',
    pending: 'bg-amber-100 text-amber-700',
    delivered: 'bg-sky-100 text-sky-700',
};
</script>

<template>
    <Head title="Gifts" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">My Gifts</h2>
                <p class="text-sm text-neutral-500">Track the gifts you've sent and received.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="mb-6 inline-flex rounded-full bg-neutral-100 p-1">
                    <button
                        @click="tab = 'sent'"
                        class="rounded-full px-5 py-2 text-sm font-semibold transition"
                        :class="tab === 'sent' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-900'"
                    >Sent ({{ sent.length }})</button>
                    <button
                        @click="tab = 'received'"
                        class="rounded-full px-5 py-2 text-sm font-semibold transition"
                        :class="tab === 'received' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-900'"
                    >Received ({{ received.length }})</button>
                </div>

                <div v-if="list.length" class="space-y-3">
                    <Link
                        v-for="gift in list"
                        :key="gift.id"
                        :href="route('gifts.show', gift.id)"
                        class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-neutral-200/70 transition hover:shadow-md"
                    >
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-neutral-100">
                            <img v-if="gift.wishlist_item?.image_url" :src="gift.wishlist_item.image_url" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-2xl">🎁</div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-neutral-900">{{ gift.wishlist_item?.title || 'Gift' }}</p>
                            <p class="mt-0.5 text-xs text-neutral-500">
                                {{ tab === 'sent' ? `To ${gift.recipient?.name}` : `From ${gift.buyer?.name}` }}
                                · {{ fmtDate(gift.created_at) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-neutral-900">{{ money(gift.amount) }}</p>
                            <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide" :class="statusBadge[gift.status] || 'bg-neutral-100 text-neutral-600'">{{ gift.status }}</span>
                        </div>
                    </Link>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
                    <div class="text-4xl">🎁</div>
                    <p class="mt-4 text-lg font-bold text-neutral-900">{{ tab === 'sent' ? 'No gifts sent yet' : 'No gifts received yet' }}</p>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-neutral-500">
                        {{ tab === 'sent' ? 'Browse a registry and send a thoughtful gift to someone you love.' : 'Gifts your guests send will appear here.' }}
                    </p>
                    <Link v-if="tab === 'sent'" href="/shop" class="mt-6 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">Browse the shop</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
