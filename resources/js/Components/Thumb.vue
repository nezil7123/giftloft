<script setup>
/**
 * A square thumbnail that fails quietly.
 *
 * A plain <img> whose file is missing renders its alt text instead, which
 * spills out of small fixed-size tiles and wrecks the layout around it — the
 * gift-addon pickers at checkout are 56px boxes, so "Premium Gift Box" lands
 * on top of the price. Here a load error swaps in a neutral placeholder, and
 * the alt text is empty because every caller already prints the name next to
 * the image.
 */
import { ref, watch } from 'vue';

const props = defineProps({
    src: { type: String, default: null },
    /** Shown when there is no image at all, rather than when one fails. */
    placeholder: { type: String, default: '—' },
});

const failed = ref(false);

// Retry when the caller points at a different file.
watch(() => props.src, () => { failed.value = false; });
</script>

<template>
    <img v-if="src && !failed" :src="src" alt="" class="h-full w-full object-cover"
        @error="failed = true" />
    <span v-else class="flex h-full w-full items-center justify-center text-lg text-neutral-300">
        {{ placeholder }}
    </span>
</template>
