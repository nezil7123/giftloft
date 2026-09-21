<script setup>
/**
 * Guest RSVP form, shared by the public event website and the invitation page.
 *
 * Templates range from bright editorial to near-black cinematic, so everything
 * here is driven by a `theme` prop rather than fixed colours.
 */
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    event: { type: Object, required: true },
    settings: { type: Object, default: () => ({ enabled: true, ask_meal: true, ask_accommodation: true, deadline: null }) },
    theme: { type: String, default: 'light' }, // 'light' | 'dark'
});

const dark = computed(() => props.theme === 'dark');
const submitted = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    status: 'attending',
    party_size: 1,
    meal_preference: null,
    needs_accommodation: false,
    note: '',
});

const attending = computed(() => form.status === 'attending');

const deadlinePassed = computed(() => {
    if (!props.settings?.deadline) return false;
    const d = new Date(props.settings.deadline);
    if (Number.isNaN(d.getTime())) return false;
    d.setHours(23, 59, 59, 999);
    return Date.now() > d.getTime();
});

const closed = computed(() => !props.settings?.enabled || deadlinePassed.value);

const deadlineLabel = computed(() => {
    if (!props.settings?.deadline) return '';
    const d = new Date(props.settings.deadline);
    return Number.isNaN(d.getTime())
        ? ''
        : d.toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' });
});

const flashSuccess = computed(() => usePage().props.flash?.success);

const submit = () => {
    form.post(route('public.event.rsvp', props.event.share_code), {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true;
            form.reset();
        },
    });
};

// ── themed class helpers ──
const card = computed(() =>
    dark.value
        ? 'border border-white/15 bg-white/[0.06] backdrop-blur-xl'
        : 'border border-neutral-200/80 bg-white shadow-xl shadow-neutral-900/5'
);
const heading = computed(() => (dark.value ? 'text-white' : 'text-neutral-950'));
const muted = computed(() => (dark.value ? 'text-white/60' : 'text-neutral-500'));
const label = computed(() => (dark.value ? 'text-white/70' : 'text-neutral-700'));
const field = computed(() =>
    dark.value
        ? 'w-full rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm text-white placeholder-white/40 outline-none transition focus:border-white/40 focus:bg-white/15'
        : 'w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 outline-none transition focus:border-neutral-900'
);
const choice = (active) =>
    active
        ? (dark.value
            ? 'border-white/60 bg-white text-neutral-900'
            : 'border-neutral-900 bg-neutral-900 text-white')
        : (dark.value
            ? 'border-white/20 text-white/70 hover:border-white/40'
            : 'border-neutral-300 text-neutral-600 hover:border-neutral-500');
</script>

<template>
    <section id="rsvp" class="px-6 py-20 sm:px-10">
        <div class="mx-auto w-full max-w-xl">
            <div :class="['rounded-3xl p-8 sm:p-10', card]">

                <!-- Confirmation -->
                <div v-if="submitted" class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/15 text-3xl">🎉</div>
                    <h3 :class="['mt-5 text-2xl font-bold tracking-tight', heading]">Thank you!</h3>
                    <p :class="['mt-2 text-sm leading-6', muted]">
                        {{ flashSuccess || "Your response has been saved." }}
                    </p>
                    <button type="button" @click="submitted = false"
                        :class="['mt-6 text-xs font-semibold underline underline-offset-4', muted]">
                        Change my response
                    </button>
                </div>

                <!-- Closed -->
                <div v-else-if="closed" class="text-center">
                    <h3 :class="['text-2xl font-bold tracking-tight', heading]">RSVPs are closed</h3>
                    <p :class="['mt-2 text-sm leading-6', muted]">
                        <template v-if="deadlinePassed">Replies closed on {{ deadlineLabel }}. Please contact the hosts directly.</template>
                        <template v-else>The hosts aren't collecting replies here.</template>
                    </p>
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="submit" class="space-y-6">
                    <div class="text-center">
                        <p :class="['text-[11px] font-semibold uppercase tracking-[0.35em]', muted]">RSVP</p>
                        <h3 :class="['mt-3 text-3xl font-bold tracking-tight', heading]">Will you join us?</h3>
                        <p v-if="deadlineLabel" :class="['mt-2 text-sm', muted]">Kindly reply by {{ deadlineLabel }}</p>
                    </div>

                    <!-- Attending or not -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="form.status = 'attending'"
                            :class="['rounded-xl border px-4 py-3 text-sm font-semibold transition', choice(attending)]">
                            Joyfully accepts
                        </button>
                        <button type="button" @click="form.status = 'not_attending'"
                            :class="['rounded-xl border px-4 py-3 text-sm font-semibold transition', choice(!attending)]">
                            Regretfully declines
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label :class="['mb-1.5 block text-xs font-semibold', label]">Your name</label>
                            <input v-model="form.name" type="text" required :class="field" placeholder="Priya Sharma" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label :class="['mb-1.5 block text-xs font-semibold', label]">Email</label>
                            <input v-model="form.email" type="email" :class="field" placeholder="you@example.com" />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-400">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label :class="['mb-1.5 block text-xs font-semibold', label]">Phone <span class="font-normal opacity-60">(optional)</span></label>
                            <input v-model="form.phone" type="tel" :class="field" placeholder="+91 98765 43210" />
                        </div>
                    </div>

                    <!-- Attendee-only questions -->
                    <div v-if="attending" class="space-y-5">
                        <div>
                            <label :class="['mb-1.5 block text-xs font-semibold', label]">How many of you are coming?</label>
                            <input v-model.number="form.party_size" type="number" min="1" max="20" :class="field" />
                            <p v-if="form.errors.party_size" class="mt-1 text-xs text-rose-400">{{ form.errors.party_size }}</p>
                        </div>

                        <div v-if="settings.ask_meal">
                            <label :class="['mb-1.5 block text-xs font-semibold', label]">Meal preference</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="form.meal_preference = 'veg'"
                                    :class="['rounded-xl border px-4 py-2.5 text-sm font-semibold transition', choice(form.meal_preference === 'veg')]">
                                    🥗 Vegetarian
                                </button>
                                <button type="button" @click="form.meal_preference = 'non_veg'"
                                    :class="['rounded-xl border px-4 py-2.5 text-sm font-semibold transition', choice(form.meal_preference === 'non_veg')]">
                                    🍗 Non-vegetarian
                                </button>
                            </div>
                            <p v-if="form.errors.meal_preference" class="mt-1 text-xs text-rose-400">{{ form.errors.meal_preference }}</p>
                        </div>

                        <label v-if="settings.ask_accommodation"
                            :class="['flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition',
                                     dark ? 'border-white/15 hover:border-white/30' : 'border-neutral-300 hover:border-neutral-500']">
                            <input v-model="form.needs_accommodation" type="checkbox"
                                class="h-4 w-4 rounded border-neutral-400 text-indigo-600 focus:ring-indigo-500" />
                            <span :class="['text-sm', label]">I'll need accommodation</span>
                        </label>
                    </div>

                    <div>
                        <label :class="['mb-1.5 block text-xs font-semibold', label]">
                            A note for the hosts <span class="font-normal opacity-60">(optional)</span>
                        </label>
                        <textarea v-model="form.note" rows="3" :class="field" placeholder="Any allergies, questions or wishes…"></textarea>
                        <p v-if="form.errors.note" class="mt-1 text-xs text-rose-400">{{ form.errors.note }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        :class="['gl-btn w-full rounded-full px-8 py-3.5 text-sm font-bold transition hover:scale-[1.02] disabled:opacity-60',
                                 dark ? 'bg-white text-neutral-900' : 'bg-neutral-900 text-white']">
                        {{ form.processing ? 'Sending…' : 'Send my response' }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
