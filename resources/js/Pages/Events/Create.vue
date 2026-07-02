<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    eventTypes: { type: Object, required: true },
});

const form = useForm({
    title: '',
    type: '',
    description: '',
    starts_at: '',
    ends_at: '',
    location: '',
    venue: '',
    cover_photo_url: '',
    is_public: true,
    status: 'draft',
});

const submit = () => form.post(route('events.store'));

// Emoji + gradient per event type (full class strings so Tailwind JIT keeps them)
const typeMeta = {
    wedding:      { emoji: '💍', grad: 'from-rose-500 to-pink-700' },
    birthday:     { emoji: '🎂', grad: 'from-amber-400 to-orange-600' },
    engagement:   { emoji: '💞', grad: 'from-fuchsia-500 to-purple-700' },
    anniversary:  { emoji: '💕', grad: 'from-red-400 to-rose-600' },
    baby_shower:  { emoji: '👶', grad: 'from-sky-400 to-blue-700' },
    proposal:     { emoji: '💜', grad: 'from-violet-500 to-purple-800' },
    graduation:   { emoji: '🎓', grad: 'from-indigo-500 to-blue-700' },
    housewarming: { emoji: '🏡', grad: 'from-emerald-400 to-teal-700' },
    other:        { emoji: '🎉', grad: 'from-indigo-500 to-violet-700' },
};
const meta = computed(() => typeMeta[form.type] ?? { emoji: '✨', grad: 'from-neutral-400 to-neutral-600' });

const fmtPreviewDate = computed(() => {
    if (!form.starts_at) return 'Date to be announced';
    return new Date(form.starts_at).toLocaleString(undefined, { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
});

const inputClass =
    'block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500';
const labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
</script>

<template>
    <Head title="Create Event" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <Link :href="route('events.index')" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to events</Link>
                <h2 class="mt-1 text-xl font-bold text-neutral-900">Create a new event</h2>
            </div>
        </template>

        <div class="py-10">
            <form @submit.prevent="submit" class="mx-auto grid max-w-6xl items-start gap-8 px-4 sm:px-6 lg:grid-cols-[1fr_22rem] lg:px-8">

                <!-- ══ Left: the form ══ -->
                <div class="space-y-6">

                    <!-- 1 · What are you celebrating? -->
                    <section class="gl-enter rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">1</span>
                            <h3 class="text-base font-bold text-neutral-900">What are you celebrating?</h3>
                        </div>
                        <div class="mt-5 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                            <button
                                v-for="(label, key) in eventTypes"
                                :key="key"
                                type="button"
                                @click="form.type = key"
                                class="group flex flex-col items-center gap-2 rounded-2xl border-2 px-2 py-4 transition"
                                :class="form.type === key ? 'border-indigo-500 bg-indigo-50/60 ring-2 ring-indigo-100' : 'border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50'"
                            >
                                <span class="text-3xl transition group-hover:scale-110" :class="{ 'scale-110': form.type === key }">{{ typeMeta[key]?.emoji ?? '✨' }}</span>
                                <span class="text-xs font-semibold" :class="form.type === key ? 'text-indigo-700' : 'text-neutral-600'">{{ label }}</span>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.type" />

                        <div class="mt-6">
                            <label for="title" :class="labelClass">Event title <span class="text-rose-500">*</span></label>
                            <input id="title" v-model="form.title" type="text" :class="inputClass" placeholder="e.g. Sarah & James's Wedding" autofocus />
                            <InputError class="mt-1.5" :message="form.errors.title" />
                        </div>
                    </section>

                    <!-- 2 · When & where -->
                    <section class="gl-enter gl-d1 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">2</span>
                            <h3 class="text-base font-bold text-neutral-900">When & where</h3>
                        </div>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="starts_at" :class="labelClass">Starts</label>
                                <input id="starts_at" v-model="form.starts_at" type="datetime-local" :class="inputClass" />
                                <InputError class="mt-1.5" :message="form.errors.starts_at" />
                            </div>
                            <div>
                                <label for="ends_at" :class="labelClass">Ends</label>
                                <input id="ends_at" v-model="form.ends_at" type="datetime-local" :class="inputClass" />
                                <InputError class="mt-1.5" :message="form.errors.ends_at" />
                            </div>
                            <div>
                                <label for="venue" :class="labelClass">Venue</label>
                                <input id="venue" v-model="form.venue" type="text" :class="inputClass" placeholder="e.g. The Taj Mahal Palace" />
                                <InputError class="mt-1.5" :message="form.errors.venue" />
                            </div>
                            <div>
                                <label for="location" :class="labelClass">City / location</label>
                                <input id="location" v-model="form.location" type="text" :class="inputClass" placeholder="e.g. Mumbai, India" />
                                <InputError class="mt-1.5" :message="form.errors.location" />
                            </div>
                        </div>
                    </section>

                    <!-- 3 · Tell the story -->
                    <section class="gl-enter gl-d2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">3</span>
                            <h3 class="text-base font-bold text-neutral-900">Tell the story</h3>
                        </div>
                        <div class="mt-5 space-y-5">
                            <div>
                                <label for="description" :class="labelClass">Description</label>
                                <textarea id="description" v-model="form.description" rows="4" :class="inputClass" placeholder="Tell your guests about the celebration…"></textarea>
                                <InputError class="mt-1.5" :message="form.errors.description" />
                            </div>
                            <div>
                                <label for="cover" :class="labelClass">Cover photo URL <span class="font-normal text-neutral-400">(you can upload more photos later)</span></label>
                                <input id="cover" v-model="form.cover_photo_url" type="url" :class="inputClass" placeholder="https://…" />
                                <InputError class="mt-1.5" :message="form.errors.cover_photo_url" />
                            </div>
                        </div>
                    </section>

                    <!-- 4 · Publish -->
                    <section class="gl-enter gl-d3 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">4</span>
                            <h3 class="text-base font-bold text-neutral-900">Publishing</h3>
                        </div>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label :class="labelClass">Status</label>
                                <div class="grid grid-cols-2 gap-1 rounded-xl bg-neutral-100 p-1">
                                    <button type="button" @click="form.status = 'draft'"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold transition"
                                        :class="form.status === 'draft' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-800'">📝 Draft</button>
                                    <button type="button" @click="form.status = 'published'"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold transition"
                                        :class="form.status === 'published' ? 'bg-white text-emerald-700 shadow-sm' : 'text-neutral-500 hover:text-neutral-800'">🚀 Published</button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.status" />
                            </div>
                            <div>
                                <label :class="labelClass">Visibility</label>
                                <button
                                    type="button"
                                    @click="form.is_public = !form.is_public"
                                    class="flex w-full items-center justify-between rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-left transition hover:border-neutral-300"
                                >
                                    <span class="text-sm text-neutral-700">{{ form.is_public ? '🌍 Public — anyone with the link' : '🔒 Private — only you' }}</span>
                                    <span class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition" :class="form.is_public ? 'bg-indigo-600' : 'bg-neutral-300'">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition" :class="form.is_public ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </span>
                                </button>
                                <InputError class="mt-1.5" :message="form.errors.is_public" />
                            </div>
                        </div>
                    </section>

                    <div class="flex items-center justify-end gap-3 pb-4">
                        <Link :href="route('events.index')" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="gl-btn inline-flex items-center gap-2 rounded-full bg-indigo-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating…</span>
                            <template v-else>
                                Create event
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- ══ Right: live preview ══ -->
                <aside class="gl-enter gl-d2 lg:sticky lg:top-24">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-neutral-400">Live preview</p>
                    <div class="overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-neutral-200/70">
                        <!-- Cover -->
                        <div class="relative flex aspect-[16/10] items-center justify-center overflow-hidden bg-gradient-to-br" :class="meta.grad">
                            <img v-if="form.cover_photo_url" :src="form.cover_photo_url" alt="" class="absolute inset-0 h-full w-full object-cover" />
                            <span v-else class="gl-float-soft text-6xl drop-shadow-lg">{{ meta.emoji }}</span>
                            <span class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide backdrop-blur"
                                :class="form.status === 'published' ? 'bg-emerald-500/90 text-white' : 'bg-white/85 text-neutral-700'">
                                {{ form.status === 'published' ? 'Live' : 'Draft' }}
                            </span>
                        </div>
                        <!-- Body -->
                        <div class="p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-500">{{ form.type ? eventTypes[form.type] : 'Your celebration' }}</p>
                            <h4 class="mt-1.5 truncate text-lg font-bold" :class="form.title ? 'text-neutral-900' : 'text-neutral-300'">
                                {{ form.title || 'Your event title' }}
                            </h4>
                            <div class="mt-3 space-y-1.5 text-sm text-neutral-500">
                                <p class="flex items-center gap-2"><span>📅</span>{{ fmtPreviewDate }}</p>
                                <p v-if="form.venue || form.location" class="flex items-center gap-2"><span>📍</span>{{ [form.venue, form.location].filter(Boolean).join(' · ') }}</p>
                                <p class="flex items-center gap-2"><span>{{ form.is_public ? '🌍' : '🔒' }}</span>{{ form.is_public ? 'Public event page' : 'Private event' }}</p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-xs leading-5 text-neutral-400">✨ After creating, you'll pick a website & invitation template, upload photos, and build your registry.</p>
                </aside>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
