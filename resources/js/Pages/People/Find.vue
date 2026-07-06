<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    query: { type: String, default: '' },
    searched: { type: Boolean, default: false },
    match: { type: Object, default: null },
});

const q = ref(props.query);
const searching = ref(false);

const search = () => {
    if (!q.value.trim()) return;
    searching.value = true;
    router.get(route('people.find'), { q: q.value.trim() }, {
        preserveState: true,
        onFinish: () => (searching.value = false),
    });
};
</script>

<template>
    <Head title="Find people" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-neutral-900">Find people</h2>
                <p class="text-sm text-neutral-500">Look up a friend by their exact email, phone number, or @username.</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <!-- Search -->
                <form @submit.prevent="search" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                    <label class="text-sm font-semibold text-neutral-800">Email, phone number, or username</label>
                    <div class="mt-2 flex gap-3">
                        <input
                            v-model="q"
                            type="text"
                            placeholder="friend@example.com, 98765 43210, or @sarah-james"
                            class="block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button
                            type="submit"
                            :disabled="searching || !q.trim()"
                            class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <svg v-if="!searching" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <svg v-else class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                            Search
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-neutral-400">The full email, number, or username must match exactly — you can't browse or search by name.</p>
                </form>

                <!-- Match -->
                <div v-if="match" class="mt-6 flex items-center gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-xl font-black text-white">
                        <img v-if="match.avatar_url" :src="match.avatar_url" :alt="match.name" class="h-full w-full object-cover" />
                        <span v-else>{{ match.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-bold text-neutral-900">{{ match.name }}</p>
                        <p class="text-xs text-neutral-500">{{ match.username ? `@${match.username}` : 'Found on Gift Loft' }}</p>
                    </div>
                    <Link :href="route('public.profile', match.username ?? match.id)" class="inline-flex shrink-0 items-center justify-center rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">View profile</Link>
                </div>

                <!-- No match -->
                <div v-else-if="searched" class="mt-6 rounded-2xl border border-dashed border-neutral-300 bg-white px-8 py-12 text-center">
                    <p class="text-3xl">🔍</p>
                    <p class="mt-3 text-base font-bold text-neutral-900">No one found</p>
                    <p class="mt-2 text-sm text-neutral-500">Nobody on Gift Loft matches that exactly — double-check the spelling, or invite them to join!</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
