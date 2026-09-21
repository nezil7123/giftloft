<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    rsvps: { type: Array, default: () => [] },
    summary: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const tiles = computed(() => [
    { label: 'Attending', value: props.summary.attending ?? 0, accent: 'text-emerald-600' },
    { label: 'Total guests', value: props.summary.head_count ?? 0, accent: 'text-indigo-600' },
    { label: 'Vegetarian', value: props.summary.veg ?? 0, accent: 'text-lime-600' },
    { label: 'Non-veg', value: props.summary.non_veg ?? 0, accent: 'text-orange-600' },
    { label: 'Need a room', value: props.summary.accommodation ?? 0, accent: 'text-violet-600' },
    { label: "Can't make it", value: props.summary.not_attending ?? 0, accent: 'text-rose-600' },
]);

const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'short' }) : '');

const mealLabel = { veg: '🥗 Veg', non_veg: '🍗 Non-veg' };

// Client-side CSV so hosts can hand numbers to a caterer without any backend work.
const downloadCsv = () => {
    const head = ['Name', 'Email', 'Phone', 'Status', 'Guests', 'Meal', 'Accommodation', 'Note', 'Replied'];
    const rows = props.rsvps.map((r) => [
        r.name, r.email ?? '', r.phone ?? '',
        r.status === 'attending' ? 'Attending' : 'Not attending',
        r.status === 'attending' ? r.party_size : '',
        r.meal_preference === 'veg' ? 'Veg' : r.meal_preference === 'non_veg' ? 'Non-veg' : '',
        r.needs_accommodation ? 'Yes' : 'No',
        (r.note ?? '').replace(/\s+/g, ' '),
        new Date(r.created_at).toLocaleDateString(),
    ]);
    const esc = (v) => `"${String(v).replace(/"/g, '""')}"`;
    const csv = [head, ...rows].map((r) => r.map(esc).join(',')).join('\n');
    const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
    const a = document.createElement('a');
    a.href = url;
    a.download = `${props.event.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}-rsvps.csv`;
    a.click();
    URL.revokeObjectURL(url);
};
</script>

<template>
    <Head :title="`RSVPs — ${event.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-neutral-900">RSVPs — {{ event.title }}</h2>
                    <p class="text-sm text-neutral-500">
                        {{ summary.responses ?? 0 }} {{ (summary.responses ?? 0) === 1 ? 'response' : 'responses' }} so far
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button v-if="rsvps.length" type="button" @click="downloadCsv"
                        class="inline-flex items-center gap-2 rounded-full border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-50">
                        Export CSV
                    </button>
                    <Link :href="route('events.show', event.id)"
                        class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700">
                        Back to event
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div v-if="!settings.enabled" class="mb-6 rounded-2xl bg-amber-50 px-5 py-4 text-sm text-amber-800 ring-1 ring-amber-200">
                    RSVPs are currently switched off for this event — guests can't reply.
                    <Link :href="route('events.design.edit', event.id)" class="font-semibold underline">Turn them on</Link>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
                    <div v-for="tile in tiles" :key="tile.label"
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-200/70">
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">{{ tile.label }}</p>
                        <p class="mt-1 text-3xl font-bold tabular-nums" :class="tile.accent">{{ tile.value }}</p>
                    </div>
                </div>

                <!-- Responses -->
                <div class="mt-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-200/70">
                    <div v-if="!rsvps.length" class="px-6 py-16 text-center">
                        <div class="text-4xl">💌</div>
                        <p class="mt-4 text-sm font-semibold text-neutral-900">No replies yet</p>
                        <p class="mt-1 text-sm text-neutral-500">Share your event link and responses will appear here.</p>
                    </div>

                    <table v-else class="w-full text-left text-sm">
                        <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Guest</th>
                                <th class="px-5 py-3 font-semibold">Reply</th>
                                <th class="px-5 py-3 font-semibold">Guests</th>
                                <th class="px-5 py-3 font-semibold">Meal</th>
                                <th class="px-5 py-3 font-semibold">Room</th>
                                <th class="px-5 py-3 font-semibold">Note</th>
                                <th class="px-5 py-3 font-semibold">Replied</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-for="r in rsvps" :key="r.id" class="transition hover:bg-neutral-50/70">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-neutral-900">{{ r.name }}</p>
                                    <p v-if="r.email || r.phone" class="text-xs text-neutral-500">{{ r.email || r.phone }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="['rounded-full px-2.5 py-1 text-xs font-semibold',
                                        r.status === 'attending' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700']">
                                        {{ r.status === 'attending' ? 'Attending' : "Can't make it" }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 tabular-nums text-neutral-700">{{ r.status === 'attending' ? r.party_size : '—' }}</td>
                                <td class="px-5 py-3.5 text-neutral-700">{{ mealLabel[r.meal_preference] ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-neutral-700">{{ r.needs_accommodation ? 'Yes' : '—' }}</td>
                                <td class="max-w-xs px-5 py-3.5 text-neutral-600">{{ r.note || '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-neutral-500">{{ fmtDate(r.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
