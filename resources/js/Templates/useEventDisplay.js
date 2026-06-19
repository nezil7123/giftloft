import { ref, computed, onMounted, onUnmounted } from 'vue';

const TYPE_LABELS = {
    wedding: 'Wedding', birthday: 'Birthday', engagement: 'Engagement',
    anniversary: 'Anniversary', baby_shower: 'Baby Shower', proposal: 'Proposal',
    graduation: 'Graduation', housewarming: 'Housewarming', other: 'Celebration',
};

/**
 * Shared display logic for event templates.
 * @param {() => object} getEvent - getter returning the reactive event prop
 */
export function useEventDisplay(getEvent) {
    const event = computed(() => getEvent() ?? {});
    const data = computed(() => event.value.template_data ?? {});

    const typeLabel = computed(() => TYPE_LABELS[event.value.type] ?? 'Celebration');
    const hosts = computed(() => data.value.hosts || '');
    const tagline = computed(() => data.value.tagline || '');
    const dressCode = computed(() => data.value.dress_code || '');
    const rsvpNote = computed(() => data.value.rsvp_note || '');
    const schedule = computed(() => (data.value.schedule ?? []).filter((s) => s && (s.title || s.time)));
    const faqs = computed(() => (data.value.faqs ?? []).filter((f) => f && f.question));
    const location = computed(() => [event.value.venue, event.value.location].filter(Boolean).join(' · '));

    const registryUrl = computed(() => {
        const w = (event.value.wishlists ?? [])[0];
        return w?.slug ? `/r/${w.slug}` : '/shop';
    });

    const fmtFull = (iso) =>
        iso ? new Date(iso).toLocaleString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
    const fmtDate = (iso) =>
        iso ? new Date(iso).toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' }) : '';

    // Live countdown
    const now = ref(Date.now());
    let timer;
    onMounted(() => { timer = setInterval(() => (now.value = Date.now()), 1000); });
    onUnmounted(() => clearInterval(timer));

    const countdown = computed(() => {
        if (!event.value.starts_at) return null;
        const diff = new Date(event.value.starts_at).getTime() - now.value;
        if (diff <= 0) return null;
        return {
            days: Math.floor(diff / 86400000),
            hours: Math.floor((diff % 86400000) / 3600000),
            mins: Math.floor((diff % 3600000) / 60000),
            secs: Math.floor((diff % 60000) / 1000),
        };
    });

    return { event, data, typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location, registryUrl, countdown, fmtFull, fmtDate };
}
