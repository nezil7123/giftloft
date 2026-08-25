<script setup>
import { Link } from '@inertiajs/vue3';
import Seo from '@/Components/Seo.vue';
import { resolveInvitationTemplate } from '@/Templates/registry.js';
import { computed, ref } from 'vue';
import { toCanvas } from 'html-to-image';

const props = defineProps({
    event: { type: Object, required: true },
});

const template = computed(() => resolveInvitationTemplate(props.event.invitation_template));
const eventUrl = computed(() => (props.event.share_code ? `/e/${props.event.share_code}` : '/'));

const cardComponent = ref(null);
const downloading = ref(false);
const downloadError = ref(false);

const filename = () => {
    const slug = (props.event.title || 'invitation').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    return `${slug || 'invitation'}.jpg`;
};

const download = async () => {
    const el = cardComponent.value?.$el;
    if (!el || downloading.value) return;

    downloading.value = true;
    downloadError.value = false;
    try {
        // toJpeg()'s own `backgroundColor` option paints over the captured
        // DOM instead of under it (it blanks out cards with a dark/opaque
        // background of their own). Render to a canvas with no background
        // option, then composite that onto a white-filled canvas ourselves
        // — the white only ends up showing through the card's rounded
        // corners, where the source canvas is genuinely transparent.
        const sourceCanvas = await toCanvas(el, {
            pixelRatio: 3,
            cacheBust: true,
            // The card's fonts are already loaded and applied live in the
            // DOM being captured, so re-embedding them isn't needed — and
            // bunny.net's stylesheet is cross-origin without a CORS header,
            // which makes the embedding attempt fail (harmlessly) anyway.
            skipFonts: true,
        });
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = sourceCanvas.width;
        finalCanvas.height = sourceCanvas.height;
        const ctx = finalCanvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
        ctx.drawImage(sourceCanvas, 0, 0);

        const a = document.createElement('a');
        a.href = finalCanvas.toDataURL('image/jpeg', 0.95);
        a.download = filename();
        a.click();
    } catch {
        downloadError.value = true;
    } finally {
        downloading.value = false;
    }
};
</script>

<template>
    <Seo :title="`You're invited — ${event.title}`" noindex />

    <div class="flex min-h-screen flex-col items-center justify-center gap-8 bg-neutral-100 px-6 py-16">
        <component :is="template" ref="cardComponent" :event="event" />

        <div class="flex flex-col items-center gap-3">
            <div class="flex flex-wrap items-center justify-center gap-3">
                <button type="button" @click="download" :disabled="downloading"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-800 shadow-sm ring-1 ring-neutral-200 transition hover:bg-neutral-50 disabled:opacity-60">
                    <svg v-if="downloading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                    {{ downloading ? 'Preparing image…' : 'Download as JPG' }}
                </button>
                <Link :href="eventUrl" class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-neutral-700">
                    View event details &amp; RSVP
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </Link>
            </div>
            <p v-if="downloadError" class="text-xs font-medium text-rose-500">Couldn't create the image — please try again.</p>
        </div>
        <p class="text-xs text-neutral-400">Powered by ComeYay</p>
    </div>
</template>
