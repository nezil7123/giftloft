<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-6 py-12">
        <div class="mb-8">
            <Link href="/" class="flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-base font-bold text-white shadow-sm">G</div>
                <div>
                    <p class="text-base font-semibold tracking-tight text-slate-950">Gift Loft</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Event gifting</p>
                </div>
            </Link>
        </div>

        <div class="w-full max-w-md rounded-[1.75rem] bg-white px-8 py-8 shadow-sm shadow-slate-200 ring-1 ring-slate-200/60">
            <slot />
        </div>
    </div>
</template>
