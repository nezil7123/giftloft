<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminNav from './Partials/AdminNav.vue';
import Pager from './Partials/Pager.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    users: { type: Object, required: true }, // paginator
    filters: { type: Object, default: () => ({}) },
});

const me = computed(() => usePage().props.auth?.user);

const q = ref(props.filters?.q ?? '');
const search = () => router.get(route('admin.users.index'), q.value ? { q: q.value } : {}, { preserveState: true, preserveScroll: true });

const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' }) : '—');

const toggleAdmin = (u) => router.put(route('admin.users.toggle-admin', u.id), {}, { preserveScroll: true });

const confirmingDelete = ref(null);
const destroy = () => {
    router.delete(route('admin.users.destroy', confirmingDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => (confirmingDelete.value = null),
    });
};
</script>

<template>
    <Head title="Admin — Users" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Users</h2>
                <p class="text-sm text-neutral-500">Search accounts, grant admin access, remove abuse.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminNav />

                <form @submit.prevent="search" class="mb-5 w-full sm:max-w-xs">
                    <input v-model="q" type="search" placeholder="Search by name or email…" @change="search"
                        class="block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" />
                </form>

                <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 text-xs uppercase tracking-wider text-neutral-400">
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-4 py-4 font-semibold">Joined</th>
                                <th class="px-4 py-4 text-center font-semibold">Events</th>
                                <th class="px-4 py-4 text-center font-semibold">Wishlists</th>
                                <th class="px-4 py-4 text-center font-semibold">Gifts sent</th>
                                <th class="px-4 py-4 font-semibold">Role</th>
                                <th class="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            <tr v-for="u in users.data" :key="u.id" class="transition hover:bg-neutral-50/60">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-600">{{ u.name?.charAt(0) ?? '?' }}</div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-neutral-900">{{ u.name }} <span v-if="u.id === me?.id" class="text-xs font-normal text-neutral-400">(you)</span></p>
                                            <p class="truncate text-xs text-neutral-400">{{ u.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-500">{{ fmtDate(u.created_at) }}</td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ u.events_count }}</td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ u.wishlists_count }}</td>
                                <td class="px-4 py-3.5 text-center tabular-nums text-neutral-700">{{ u.gifts_sent_count }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="u.is_admin ? 'bg-amber-100 text-amber-700' : 'bg-neutral-100 text-neutral-500'">
                                        {{ u.is_admin ? 'Admin' : 'Member' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <template v-if="u.id !== me?.id">
                                        <button type="button" @click="toggleAdmin(u)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50">
                                            {{ u.is_admin ? 'Revoke admin' : 'Make admin' }}
                                        </button>
                                        <button type="button" @click="confirmingDelete = u" class="rounded-full px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                                    </template>
                                    <span v-else class="text-xs text-neutral-300">—</span>
                                </td>
                            </tr>
                            <tr v-if="!users.data.length">
                                <td colspan="7" class="px-6 py-12 text-center text-neutral-400">No users found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pager :paginator="users" />
            </div>
        </div>

        <!-- Delete confirm -->
        <Teleport to="body">
            <div v-if="confirmingDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="confirmingDelete = null">
                <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">
                    <p class="text-3xl">⚠️</p>
                    <h3 class="mt-3 text-base font-bold text-neutral-900">Delete {{ confirmingDelete.name }}?</h3>
                    <p class="mt-2 text-sm text-neutral-500">This permanently removes their account, events, wishlists and gift history.</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button type="button" @click="confirmingDelete = null" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</button>
                        <button type="button" @click="destroy" class="rounded-full bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">Delete account</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
