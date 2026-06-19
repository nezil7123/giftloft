<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    src: { type: String, required: true },
    designWidth: { type: Number, default: 1280 },
    ratio: { type: Number, default: 0.74 }, // visible height = width * ratio
});

const root = ref(null);
const width = ref(360);
let ro;

const recompute = () => { if (root.value) width.value = root.value.clientWidth; };

onMounted(() => {
    recompute();
    ro = new ResizeObserver(recompute);
    ro.observe(root.value);
});
onUnmounted(() => ro?.disconnect());

const scale = computed(() => width.value / props.designWidth);
const boxHeight = computed(() => width.value * props.ratio);
const iframeHeight = computed(() => props.designWidth * props.ratio);
</script>

<template>
    <div ref="root" class="relative w-full overflow-hidden bg-neutral-100" :style="{ height: boxHeight + 'px' }">
        <iframe
            :src="src"
            loading="lazy"
            scrolling="no"
            tabindex="-1"
            class="pointer-events-none absolute left-0 top-0 origin-top-left border-0"
            :style="{ width: designWidth + 'px', height: iframeHeight + 'px', transform: `scale(${scale})` }"
        ></iframe>
    </div>
</template>
