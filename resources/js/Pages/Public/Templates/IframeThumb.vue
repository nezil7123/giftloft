<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    src: { type: String, required: true },
    designWidth: { type: Number, default: 1280 },
    ratio: { type: Number, default: 0.74 }, // visible height = width * ratio
});

const root = ref(null);
const frame = ref(null);
const width = ref(360);
let ro;
let io;
let visible = false;

const recompute = () => { if (root.value) width.value = root.value.clientWidth; };

// Same-origin iframe: we can reach into its document directly. Toggle a
// class that pauses every CSS `animation` inside it (marquee/float/Ken-Burns/
// etc.) whenever the thumbnail scrolls out of view, so a gallery of 20+ live
// previews doesn't leave that many infinite animations running at once.
const syncPauseState = () => {
    try {
        const doc = frame.value?.contentDocument;
        if (doc?.documentElement) doc.documentElement.classList.toggle('gl-anim-paused', !visible);
    } catch {
        // Cross-origin or not-yet-ready; nothing to do.
    }
};

onMounted(() => {
    recompute();
    ro = new ResizeObserver(recompute);
    ro.observe(root.value);

    io = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        syncPauseState();
    }, { rootMargin: '200px' });
    io.observe(root.value);
});
onUnmounted(() => { ro?.disconnect(); io?.disconnect(); });

const scale = computed(() => width.value / props.designWidth);
const boxHeight = computed(() => width.value * props.ratio);
const iframeHeight = computed(() => props.designWidth * props.ratio);
</script>

<template>
    <div ref="root" class="relative w-full overflow-hidden bg-neutral-100" :style="{ height: boxHeight + 'px' }">
        <iframe
            ref="frame"
            :src="src"
            loading="lazy"
            scrolling="no"
            tabindex="-1"
            class="pointer-events-none absolute left-0 top-0 origin-top-left border-0"
            :style="{ width: designWidth + 'px', height: iframeHeight + 'px', transform: `scale(${scale})` }"
            @load="syncPauseState"
        ></iframe>
    </div>
</template>
