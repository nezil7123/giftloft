<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Privacy Policy" />

    <PublicLayout theme="light">
        <div class="pb-20" style="padding-top: calc(var(--nav-offset, 62px) + 3rem)">
            <div class="legal mx-auto max-w-3xl px-6 sm:px-10">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-indigo-600">Legal</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Privacy Policy</h1>
                <p class="mt-3 text-sm text-neutral-500">Effective 6 July 2026 · Applies to giftloft.app and all Gift Loft services</p>

                <h2>1. Who we are</h2>
                <p>Gift Loft ("we", "us") is an event invitation and gifting platform operated from India. This policy explains what personal data we collect, why, and the choices you have. We aim to comply with India's Digital Personal Data Protection Act, 2023 and other applicable law.</p>

                <h2>2. What we collect</h2>
                <ul>
                    <li><strong>Account details</strong> — name, email address, optional username, optional phone number, and password (stored only as a secure hash).</li>
                    <li><strong>Delivery addresses</strong> — addresses you enter at checkout, and any delivery address you choose to save on a wishlist.</li>
                    <li><strong>Content you create</strong> — events, invitation and website content, photos you upload, wishlists, gift messages, and text you ask us to print on gift packaging, stickers, or cards.</li>
                    <li><strong>Order and payment records</strong> — what you bought, amounts, and payment references from our payment processor. <strong>We never see or store your card, UPI, or banking credentials</strong>; payments are handled by Razorpay under their own privacy policy.</li>
                    <li><strong>Technical data</strong> — session cookies, security (CSRF) tokens, and basic logs needed to keep the service running and safe. We do not use third-party advertising trackers.</li>
                </ul>

                <h2>3. How we use it</h2>
                <ul>
                    <li>To operate your account, events, wishlists, and orders.</li>
                    <li>To deliver gifts — we share the delivery name, address, and phone with our delivery partners solely to fulfil the order.</li>
                    <li>To print your personalization text on packaging, stickers, and cards you ordered.</li>
                    <li>To secure the platform, prevent fraud and abuse, and comply with legal obligations.</li>
                    <li>To send transactional messages (order confirmations, account notices). We don't sell your data, and we don't send marketing without your consent.</li>
                </ul>

                <h2>4. What other people can see</h2>
                <ul>
                    <li><strong>You control visibility.</strong> Events and wishlists are only public if you publish them or set them to public; drafts and private items are never shown.</li>
                    <li><strong>Public profile</strong> — your name, username (if set), and your <em>public</em> events and wishlists appear at your profile link. You can hide your entire profile at any time, which also removes you from people search.</li>
                    <li><strong>People search</strong> — others can find you only by typing your exact email, phone number, or username. Browsing or searching by name is not possible, and search results never reveal your email or phone.</li>
                    <li><strong>Saved wishlist address</strong> — gifters see only your name and city, never the address. The full address is attached to an order server-side and shared only with delivery partners.</li>
                    <li>Gift messages are visible to the recipient of the gift.</li>
                </ul>

                <h2>5. Sharing</h2>
                <p>We share personal data only with: (a) Razorpay, to process payments; (b) delivery/courier partners, to fulfil orders; (c) service providers who host our infrastructure; and (d) authorities where the law requires it. Each receives only what it needs.</p>

                <h2>6. Retention</h2>
                <p>We keep your data while your account is active. Order and payment records are retained as required by tax and accounting law even after account deletion. Content you delete (events, wishlists, photos) is removed from public view immediately and purged from our systems in the ordinary course.</p>

                <h2>7. Your rights and controls</h2>
                <ul>
                    <li>Edit your name, username, email, and addresses in <strong>Settings</strong>.</li>
                    <li>Unpublish or delete events and wishlists at any time.</li>
                    <li>Hide your public profile entirely.</li>
                    <li>Delete your account from Settings — this permanently removes your profile, events, and wishlists.</li>
                    <li>You may request access to or correction of your data, or raise a grievance, by contacting us (Section 10).</li>
                </ul>

                <h2>8. Security</h2>
                <p>Passwords are hashed, traffic is encrypted in transit, and access to production data is restricted. No system is perfectly secure — if we learn of a breach affecting your data, we will notify you and the authorities as required by law.</p>

                <h2>9. Children</h2>
                <p>Gift Loft is not intended for children under 18. We do not knowingly process a child's data without verifiable parental consent; if you believe a child has created an account, contact us and we will remove it.</p>

                <h2>10. Contact & grievances</h2>
                <p>For privacy questions, data requests, or grievances, email <strong>privacy@giftloft.app</strong>. We respond within the timelines required by applicable law.</p>

                <h2>11. Changes</h2>
                <p>If we change this policy in a material way, we'll update the effective date above and notify you in the app or by email before the change takes effect.</p>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.legal h2 {
    margin-top: 2.25rem;
    font-size: 1.125rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: #0a0a0a;
}
.legal p {
    margin-top: 0.75rem;
    font-size: 0.9rem;
    line-height: 1.7;
    color: #525252;
}
.legal ul {
    margin-top: 0.75rem;
    list-style: disc;
    padding-left: 1.25rem;
}
.legal li {
    margin-top: 0.45rem;
    font-size: 0.9rem;
    line-height: 1.7;
    color: #525252;
}
.legal strong { color: #171717; }
</style>
