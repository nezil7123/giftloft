<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    websiteTemplates: { type: Array, required: true },
    invitationTemplates: { type: Array, required: true },
});

const inputClass = 'mt-1.5 block w-full rounded-xl border-neutral-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

const accentBg = {
    indigo: 'bg-indigo-500', neutral: 'bg-neutral-700', rose: 'bg-rose-500',
    amber: 'bg-amber-500', violet: 'bg-violet-500',
};

const d = props.event.template_data ?? {};
const form = useForm({
    template: props.event.template ?? 'classic',
    invitation_template: props.event.invitation_template ?? 'elegant',
    template_data: {
        hosts: d.hosts ?? '',
        tagline: d.tagline ?? '',
        dress_code: d.dress_code ?? '',
        rsvp_note: d.rsvp_note ?? '',
        venue_note: d.venue_note ?? '',
        travel: d.travel ?? '',
        stay: d.stay ?? '',
        schedule: Array.isArray(d.schedule) ? [...d.schedule] : [],
        faqs: Array.isArray(d.faqs) ? [...d.faqs] : [],
    },
});

const addSchedule = () => form.template_data.schedule.push({ time: '', title: '', detail: '' });
const removeSchedule = (i) => form.template_data.schedule.splice(i, 1);
const addFaq = () => form.template_data.faqs.push({ question: '', answer: '' });
const removeFaq = (i) => form.template_data.faqs.splice(i, 1);

const submit = () => form.put(route('events.design.update', props.event.id));

// ── Gallery photos ──────────────────────────────────────────────
const photos = computed(() => props.event.photos ?? []);
const hasCoverUrl = computed(() => !!props.event.cover_photo_url);
const photoInput = ref(null);
const photoForm = useForm({ photos: [] });

const uploadPhotos = (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;
    photoForm.photos = files;
    photoForm.post(route('events.photos.store', props.event.id), {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { photoForm.reset('photos'); if (photoInput.value) photoInput.value.value = ''; },
    });
};

const removePhoto = (path) => {
    router.delete(route('events.photos.destroy', props.event.id), {
        data: { path },
        preserveScroll: true,
        preserveState: true,
    });
};

const makeCover = (path) => {
    const order = [path, ...photos.value.filter((p) => p !== path)];
    router.put(route('events.photos.reorder', props.event.id), { order }, {
        preserveScroll: true,
        preserveState: true,
    });
};

const previewUrl = props.event.share_code ? `/e/${props.event.share_code}` : null;
const inviteUrl = props.event.share_code ? `/e/${props.event.share_code}/invitation` : null;
</script>

<template>
    <Head title="Design your event" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Link :href="route('events.show', event.id)" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">← Back to event</Link>
                    <h2 class="mt-1 text-xl font-bold text-neutral-900">Design — {{ event.title }}</h2>
                </div>
                <div class="flex gap-2">
                    <a v-if="previewUrl" :href="previewUrl" target="_blank" class="rounded-full border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">Preview site ↗</a>
                    <a v-if="inviteUrl" :href="inviteUrl" target="_blank" class="rounded-full border border-neutral-300 px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">Preview invite ↗</a>
                </div>
            </div>
        </template>

        <div class="py-10">
            <form @submit.prevent="submit" class="mx-auto max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- Website template -->
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <h3 class="text-base font-bold text-neutral-900">Event website template</h3>
                    <p class="text-sm text-neutral-500">Pick the design for your public event page.</p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-3">
                        <button
                            v-for="t in websiteTemplates"
                            :key="t.key"
                            type="button"
                            @click="form.template = t.key"
                            class="rounded-2xl border-2 p-5 text-left transition"
                            :class="form.template === t.key ? 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-200' : 'border-neutral-200 hover:border-neutral-300'"
                        >
                            <div class="flex items-center justify-between">
                                <span class="h-8 w-8 rounded-lg" :class="accentBg[t.accent] || 'bg-neutral-400'"></span>
                                <span v-if="form.template === t.key" class="text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </div>
                            <p class="mt-3 text-sm font-bold text-neutral-900">{{ t.name }}</p>
                            <p class="mt-1 text-xs leading-5 text-neutral-500">{{ t.description }}</p>
                        </button>
                    </div>
                </section>

                <!-- Invitation template -->
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <h3 class="text-base font-bold text-neutral-900">Invitation template</h3>
                    <p class="text-sm text-neutral-500">Pick the design for your shareable invitation card.</p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-3">
                        <button
                            v-for="t in invitationTemplates"
                            :key="t.key"
                            type="button"
                            @click="form.invitation_template = t.key"
                            class="rounded-2xl border-2 p-5 text-left transition"
                            :class="form.invitation_template === t.key ? 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-200' : 'border-neutral-200 hover:border-neutral-300'"
                        >
                            <div class="flex items-center justify-between">
                                <span class="h-8 w-8 rounded-lg" :class="accentBg[t.accent] || 'bg-neutral-400'"></span>
                                <span v-if="form.invitation_template === t.key" class="text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </div>
                            <p class="mt-3 text-sm font-bold text-neutral-900">{{ t.name }}</p>
                            <p class="mt-1 text-xs leading-5 text-neutral-500">{{ t.description }}</p>
                        </button>
                    </div>
                </section>

                <!-- Photos / gallery -->
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-neutral-900">Photos</h3>
                            <p class="text-sm text-neutral-500">Upload photos for your event gallery. The first photo is used as the cover when no cover image is set.</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-500">{{ photos.length }} / 24</span>
                    </div>

                    <!-- Upload control -->
                    <label
                        class="mt-5 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 px-6 py-8 text-center transition hover:border-indigo-400 hover:bg-indigo-50/40"
                        :class="{ 'pointer-events-none opacity-60': photoForm.processing || photos.length >= 24 }"
                    >
                        <svg class="h-8 w-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2" /></svg>
                        <span class="mt-2 text-sm font-semibold text-neutral-700">
                            {{ photoForm.processing ? 'Uploading…' : photos.length >= 24 ? 'Photo limit reached' : 'Click to upload photos' }}
                        </span>
                        <span class="mt-0.5 text-xs text-neutral-400">JPG, PNG, WebP or GIF · up to 8MB each · select multiple</span>
                        <input ref="photoInput" type="file" accept="image/*" multiple class="hidden" :disabled="photoForm.processing || photos.length >= 24" @change="uploadPhotos" />
                    </label>

                    <!-- Upload progress -->
                    <div v-if="photoForm.progress" class="mt-3 h-1.5 overflow-hidden rounded-full bg-neutral-100">
                        <div class="h-full rounded-full bg-indigo-500 transition-all" :style="`width:${photoForm.progress.percentage}%`"></div>
                    </div>
                    <p v-if="photoForm.errors.photos || photoForm['errors']['photos.0']" class="mt-2 text-sm text-rose-600">{{ photoForm.errors.photos || photoForm['errors']['photos.0'] }}</p>

                    <!-- Thumbnail grid -->
                    <div v-if="photos.length" class="mt-6 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div v-for="(p, i) in photos" :key="p" class="group relative aspect-square overflow-hidden rounded-xl ring-1 ring-neutral-200">
                            <img :src="p" class="h-full w-full object-cover" />
                            <span v-if="i === 0 && !hasCoverUrl" class="absolute left-1.5 top-1.5 rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white shadow">Cover</span>
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 bg-black/45 opacity-0 transition group-hover:opacity-100">
                                <button v-if="i !== 0" type="button" @click="makeCover(p)" class="rounded-full bg-white/90 px-3 py-1 text-[11px] font-semibold text-neutral-800 transition hover:bg-white">Make cover</button>
                                <button type="button" @click="removePhoto(p)" class="rounded-full bg-rose-500 px-3 py-1 text-[11px] font-semibold text-white transition hover:bg-rose-600">Remove</button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="mt-4 text-xs text-neutral-400">No photos yet.</p>
                </section>

                <!-- Content -->
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-200/70 sm:p-8">
                    <h3 class="text-base font-bold text-neutral-900">Content</h3>
                    <p class="text-sm text-neutral-500">These fill in your chosen templates.</p>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Hosts / couple names</label>
                            <input v-model="form.template_data.hosts" type="text" :class="inputClass" placeholder="e.g. Sarah & James" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Tagline</label>
                            <input v-model="form.template_data.tagline" type="text" :class="inputClass" placeholder="e.g. We're getting married!" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Dress code</label>
                            <input v-model="form.template_data.dress_code" type="text" :class="inputClass" placeholder="e.g. Cocktail / Black tie" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Registry / RSVP note</label>
                            <input v-model="form.template_data.rsvp_note" type="text" :class="inputClass" placeholder="A short note for the gifts section" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-neutral-800">About the venue</label>
                            <textarea v-model="form.template_data.venue_note" rows="2" :class="inputClass" placeholder="e.g. A sea-facing heritage ballroom in the heart of Colaba…"></textarea>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Getting there</label>
                            <textarea v-model="form.template_data.travel" rows="2" :class="inputClass" placeholder="Parking, transit, directions…"></textarea>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-800">Where to stay</label>
                            <textarea v-model="form.template_data.stay" rows="2" :class="inputClass" placeholder="Nearby hotels, room blocks…"></textarea>
                        </div>
                    </div>

                    <!-- Schedule repeater -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-neutral-900">Schedule</h4>
                            <button type="button" @click="addSchedule" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">+ Add item</button>
                        </div>
                        <div v-if="form.template_data.schedule.length" class="mt-3 space-y-3">
                            <div v-for="(s, i) in form.template_data.schedule" :key="i" class="grid gap-2 rounded-xl bg-neutral-50 p-3 sm:grid-cols-[120px_1fr_1fr_auto]">
                                <input v-model="s.time" type="text" class="rounded-lg border-neutral-300 text-sm" placeholder="4:00 PM" />
                                <input v-model="s.title" type="text" class="rounded-lg border-neutral-300 text-sm" placeholder="Ceremony" />
                                <input v-model="s.detail" type="text" class="rounded-lg border-neutral-300 text-sm" placeholder="Detail (optional)" />
                                <button type="button" @click="removeSchedule(i)" class="text-rose-500 hover:text-rose-700" aria-label="Remove">✕</button>
                            </div>
                        </div>
                        <p v-else class="mt-2 text-xs text-neutral-400">No schedule items yet.</p>
                    </div>

                    <!-- FAQ repeater -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-neutral-900">FAQs</h4>
                            <button type="button" @click="addFaq" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">+ Add FAQ</button>
                        </div>
                        <div v-if="form.template_data.faqs.length" class="mt-3 space-y-3">
                            <div v-for="(f, i) in form.template_data.faqs" :key="i" class="rounded-xl bg-neutral-50 p-3">
                                <div class="flex gap-2">
                                    <input v-model="f.question" type="text" class="w-full rounded-lg border-neutral-300 text-sm font-semibold" placeholder="Question" />
                                    <button type="button" @click="removeFaq(i)" class="shrink-0 text-rose-500 hover:text-rose-700" aria-label="Remove">✕</button>
                                </div>
                                <textarea v-model="f.answer" rows="2" class="mt-2 w-full rounded-lg border-neutral-300 text-sm" placeholder="Answer"></textarea>
                            </div>
                        </div>
                        <p v-else class="mt-2 text-xs text-neutral-400">No FAQs yet.</p>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('events.show', event.id)" class="rounded-full px-5 py-2.5 text-sm font-semibold text-neutral-600 transition hover:bg-neutral-100">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50">
                        {{ form.processing ? 'Saving…' : 'Save design' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
