<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Terms & Conditions" />

    <PublicLayout theme="light">
        <div class="pb-20" style="padding-top: calc(var(--nav-offset, 62px) + 3rem)">
            <div class="legal mx-auto max-w-3xl px-6 sm:px-10">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-indigo-600">Legal</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Terms &amp; Conditions</h1>
                <p class="mt-3 text-sm text-neutral-500">Effective 6 July 2026 · Please read before using Gift Loft</p>

                <h2>1. Agreement</h2>
                <p>By creating an account or using giftloft.app ("Gift Loft", the "Service"), you agree to these Terms and our <Link href="/privacy" class="font-semibold text-indigo-600 hover:text-indigo-500">Privacy Policy</Link>. If you do not agree, please do not use the Service.</p>

                <h2>2. Eligibility & your account</h2>
                <ul>
                    <li>You must be at least 18 years old, or use the Service under the supervision of a parent or guardian who accepts these Terms.</li>
                    <li>You are responsible for your account credentials and for everything done under your account. Tell us immediately if you suspect unauthorised access.</li>
                    <li>Information you provide (including delivery addresses) must be accurate — deliveries are made to the address given, and we are not responsible for failed delivery to an incorrect address.</li>
                </ul>

                <h2>3. Your content</h2>
                <ul>
                    <li>You own the content you create — events, photos, invitation text, wishlists, gift messages, and personalization text. You grant us a licence to host, display, and (for packaging, stickers, and cards) print it, solely to provide the Service.</li>
                    <li>You must have the rights to any photo or text you upload. Do not post content that is unlawful, infringing, hateful, obscene, or that exposes someone else's private information without consent.</li>
                    <li>Content you mark public (published events, public wishlists, your profile) is visible to anyone with the link. You can unpublish or delete it at any time.</li>
                    <li>We may remove content or suspend accounts that violate these Terms or the law, and where required, cooperate with lawful requests from authorities.</li>
                </ul>

                <h2>4. Purchases, payments & pricing</h2>
                <ul>
                    <li>All prices are in Indian Rupees (₹) and include the product price shown at checkout plus any selected gift add-ons (packaging, message sticker, custom card). The total is always shown before you pay.</li>
                    <li>Payments are processed by Razorpay. Your order is confirmed only after successful payment verification.</li>
                    <li>Obvious pricing errors may be cancelled and refunded in full.</li>
                    <li>Add-on prices are set at the time of order — later catalogue changes don't affect past orders.</li>
                </ul>

                <h2>5. Gifting & wishlists</h2>
                <ul>
                    <li>Buying a wishlist item marks it as claimed for the recipient. Claims are first-come, first-served.</li>
                    <li>If a wishlist owner saves a delivery address, gifts you send using it ship to that address. We show you only the recipient's name and city; you agree not to attempt to obtain the underlying address.</li>
                    <li>Gift messages and printed personalization are delivered as you wrote them — please double-check spelling. We may refuse to print content that violates Section 3.</li>
                </ul>

                <h2>6. Delivery, cancellations & refunds</h2>
                <ul>
                    <li>Delivery timelines are estimates. Risk in the goods passes on delivery to the given address.</li>
                    <li>Orders can be cancelled before dispatch for a full refund. Once dispatched, items can't be cancelled.</li>
                    <li>Damaged, defective, or wrong items: contact us within 48 hours of delivery with photos and we will replace or refund.</li>
                    <li><strong>Personalized items</strong> (printed stickers, custom cards, personalized packaging) are non-refundable unless damaged or misprinted by us.</li>
                    <li>Refunds are issued to the original payment method via Razorpay, typically within 5–7 business days of approval.</li>
                </ul>

                <h2>7. Acceptable use</h2>
                <p>You agree not to: scrape or enumerate user data; probe or disrupt the Service; use it to send spam or unsolicited invitations; misrepresent your identity; or use the people-search to harass anyone. We may rate-limit, suspend, or terminate accounts that do.</p>

                <h2>8. Our IP</h2>
                <p>The Gift Loft name, design, templates, and software are ours or our licensors'. Using the Service gives you no rights in them beyond using the Service as intended. Website and invitation templates may be used only for events created on Gift Loft.</p>

                <h2>9. Disclaimers & liability</h2>
                <ul>
                    <li>The Service is provided "as is". We don't guarantee uninterrupted availability, and event pages, wishlists, and profiles depend on content their owners provide.</li>
                    <li>To the maximum extent permitted by law, our total liability for any claim is limited to the amount you paid us for the order giving rise to the claim (or ₹1,000 where no order is involved). We are not liable for indirect or consequential losses.</li>
                    <li>Nothing in these Terms limits liability that cannot be limited under Indian law, including under the Consumer Protection Act, 2019.</li>
                </ul>

                <h2>10. Indemnity</h2>
                <p>You will indemnify us against claims arising from content you post or print requests you make that infringe someone else's rights or break the law.</p>

                <h2>11. Termination</h2>
                <p>You may delete your account at any time from Settings. We may suspend or terminate accounts that breach these Terms, with notice where practicable. Sections that by nature survive (payments owed, liability, indemnity, IP) survive termination.</p>

                <h2>12. Governing law & disputes</h2>
                <p>These Terms are governed by the laws of India. Subject to any mandatory consumer-protection rights you hold, courts at Kochi, Kerala have exclusive jurisdiction.</p>

                <h2>13. Changes & contact</h2>
                <p>We may update these Terms; material changes will be notified in the app or by email before they take effect, and the effective date above will change. Questions: <strong>support@giftloft.app</strong>.</p>
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
