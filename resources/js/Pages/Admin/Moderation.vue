<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import Pager from './Partials/Pager.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    events: { type: Object, required: true },     // paginator
    wishlists: { type: Object, required: true },  // paginator
    filters: { type: Object, default: () => ({}) },
});

const q = ref(props.filters?.q ?? '');
const search = () => router.get(route('admin.moderation.index'), q.value ? { q: q.value } : {}, { preserveState: true, preserveScroll: true });

const takedown = (e) => router.put(route('admin.events.takedown', e.id), {}, { preserveScroll: true });
const restore = (e) => router.put(route('admin.events.restore', e.id), {}, { preserveScroll: true });
const toggleWishlist = (w) => router.put(route('admin.wishlists.toggle', w.id), {}, { preserveScroll: true });

const isLive = (e) => e.status === 'published' && e.is_public;
</script>

<template>
    <Head title="Admin — Moderation" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Moderation</h2>
                <p class="text-sm text-neutral-500">Review every event page and registry — take down anything that breaks the rules.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <form @submit.prevent="search" class="mb-5 w-full sm:max-w-xs">
                    <input v-model="q" type="search" placeholder="Search events & registries…" @change="search"
                        class="block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" />
                </form>

                <!-- Events -->
                <h3 class="mb-3 text-sm font-bold uppercase tracking-widest text-neutral-400">Events</h3>
                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">Event</th>
                                <th class="px-4 py-4 font-semibold">Owner</th>
                                <th class="px-4 py-4 text-center font-semibold">Registries</th>
                                <th class="px-4 py-4 text-center font-semibold">Gifts</th>
                                <th class="px-4 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="e in events.data" :key="e.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5">
                                    <p class="font-semibold text-neutral-900">{{ e.title }}</p>
                                    <p class="text-xs capitalize text-neutral-400">{{ (e.type || '').replace('_', ' ') }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-neutral-700">{{ e.user?.name }}</p>
                                    <p class="text-xs text-neutral-400">{{ e.user?.email }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ e.wishlists_count }}</td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ e.gifts_count }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="isLive(e) ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-100 text-neutral-500'">
                                        {{ isLive(e) ? 'Live' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a v-if="e.share_code" :href="`/e/${e.share_code}`" target="_blank" class="rounded-full px-3 py-1.5 text-xs font-semibold text-neutral-500 transition hover:bg-neutral-100">View ↗</a>
                                    <button v-if="isLive(e)" type="button" @click="takedown(e)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Take down</button>
                                    <button v-else type="button" @click="restore(e)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-50">Restore</button>
                                </td>
                            </tr>
                            <tr v-if="!events.data.length">
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-400">No events found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pager :paginator="events" />

                <!-- Registries -->
                <h3 class="mb-3 mt-10 text-sm font-bold uppercase tracking-widest text-neutral-400">Registries</h3>
                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">Registry</th>
                                <th class="px-4 py-4 font-semibold">Owner</th>
                                <th class="px-4 py-4 font-semibold">Event</th>
                                <th class="px-4 py-4 text-center font-semibold">Items</th>
                                <th class="px-4 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="w in wishlists.data" :key="w.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5">
                                    <p class="font-semibold text-neutral-900">{{ w.name }}</p>
                                    <p class="text-xs text-neutral-400">/r/{{ w.slug }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-700">{{ w.user?.name }}</td>
                                <td class="px-4 py-3.5 text-neutral-500">{{ w.event?.title ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ w.items_count }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="w.active ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-100 text-neutral-500'">
                                        {{ w.active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a v-if="w.slug && w.active" :href="`/r/${w.slug}`" target="_blank" class="rounded-full px-3 py-1.5 text-xs font-semibold text-neutral-500 transition hover:bg-neutral-100">View ↗</a>
                                    <button type="button" @click="toggleWishlist(w)" class="rounded-full px-3 py-1.5 text-xs font-semibold transition" :class="w.active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50'">
                                        {{ w.active ? 'Deactivate' : 'Reactivate' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!wishlists.data.length">
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-400">No registries found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pager :paginator="wishlists" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
