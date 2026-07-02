<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const inputClass =
    'block w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500';
</script>

<template>
    <GuestLayout>
        <Head title="Create account" />

        <div class="gl-enter">
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-950">Start celebrating</h1>
            <p class="mt-2 text-sm text-neutral-500">Free to start — your first event is minutes away.</p>
        </div>

        <!-- Google first — the fast path -->
        <Link
            :href="route('auth.google')"
            class="gl-enter gl-d1 mt-8 inline-flex w-full items-center justify-center gap-3 rounded-xl bg-white px-4 py-3 text-sm font-semibold text-neutral-700 ring-1 ring-neutral-200 transition hover:bg-neutral-50 hover:ring-neutral-300"
        >
            <svg class="h-4.5 w-4.5" width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.5 12.27c0-.85-.08-1.66-.22-2.45H12v4.64h6.45a5.52 5.52 0 0 1-2.39 3.62v3h3.87c2.26-2.09 3.57-5.16 3.57-8.81z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3c-1.08.72-2.45 1.15-4.06 1.15-3.13 0-5.78-2.11-6.73-4.96H1.29v3.1A11.99 11.99 0 0 0 12 24z"/>
                <path fill="#FBBC05" d="M5.27 14.28A7.16 7.16 0 0 1 4.9 12c0-.79.14-1.56.37-2.28v-3.1H1.29a12.04 12.04 0 0 0 0 10.76l3.98-3.1z"/>
                <path fill="#EA4335" d="M12 4.77c1.76 0 3.34.61 4.59 1.8l3.43-3.43C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.69 1.29 6.62l3.98 3.1C6.22 6.88 8.87 4.77 12 4.77z"/>
            </svg>
            Sign up with Google
        </Link>

        <div class="gl-enter gl-d1 my-7 flex items-center gap-4">
            <span class="h-px flex-1 bg-neutral-200"></span>
            <span class="text-xs font-medium uppercase tracking-widest text-neutral-400">or with email</span>
            <span class="h-px flex-1 bg-neutral-200"></span>
        </div>

        <form @submit.prevent="submit" class="gl-enter gl-d2 space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-neutral-700">Your name</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    :class="inputClass"
                    placeholder="e.g. Sarah Sharma"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-neutral-700">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    :class="inputClass"
                    placeholder="you@example.com"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-neutral-700">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        :class="inputClass"
                        placeholder="8+ characters"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-1.5" :message="form.errors.password" />
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-neutral-700">Confirm</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        :class="inputClass"
                        placeholder="Repeat password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="gl-btn inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-500 disabled:opacity-50"
            >
                <span v-if="form.processing">Creating your account…</span>
                <template v-else>
                    Create account
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </template>
            </button>

            <p class="text-center text-xs leading-5 text-neutral-400">By continuing you agree to our terms — celebrations only, no spam. 🎉</p>
        </form>

        <p class="gl-enter gl-d3 mt-6 text-center text-sm text-neutral-500">
            Already have an account?
            <Link :href="route('login')" class="font-semibold text-indigo-600 transition hover:text-indigo-500">Log in</Link>
        </p>
    </GuestLayout>
</template>
