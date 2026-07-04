<?php

namespace App\Support;

/**
 * Content for the "How it works" guide pages linked from the user dashboard.
 * Single source of truth so the controller and the Vue page stay in sync —
 * add a guide here and it becomes reachable at /help/{slug}.
 */
class HelpGuides
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'wishlist' => [
                'slug' => 'wishlist',
                'emoji' => '🎁',
                'gradient' => 'from-rose-500 to-orange-500',
                'mockupLabel' => 'giftloft.app/r/our-wishlist',
                'title' => 'How wishlists work',
                'subtitle' => 'Curate exactly what you\'d love to receive, so guests never have to guess — and gifts never get duplicated.',
                'steps' => [
                    ['title' => 'Create your event', 'description' => 'Every wishlist lives under an event — a wedding, birthday, baby shower, or any celebration you\'re planning.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['title' => 'Open "Build a wishlist"', 'description' => 'From your dashboard or the event page, start a new wishlist and give it a name your guests will recognise.', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    ['title' => 'Add items', 'description' => 'Pick products from our shop or paste a link to anything from anywhere — set a price and how many you\'d like.', 'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                    ['title' => 'Share the link', 'description' => 'Send your wishlist link (or let it live on your event page) so guests can see what\'s still needed and claim an item.', 'icon' => 'M8.684 13.342a4.5 4.5 0 010-2.684m0 2.684a4.5 4.5 0 010 2.684m0-2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a4.5 4.5 0 108.632-2.684 4.5 4.5 0 00-8.632 2.684zm0 12.684a4.5 4.5 0 108.632 2.684 4.5 4.5 0 00-8.632-2.684z'],
                    ['title' => 'Track claims automatically', 'description' => 'When someone gifts an item, it\'s marked claimed instantly — no more spreadsheets or double gifts.', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ],
                'cta' => ['label' => 'Build a wishlist', 'route' => 'wishlists.create'],
                'secondaryCta' => ['label' => 'View my wishlists', 'route' => 'wishlists.index'],
            ],

            'gifting' => [
                'slug' => 'gifting',
                'emoji' => '🛍️',
                'gradient' => 'from-emerald-500 to-teal-600',
                'mockupLabel' => 'Order #1042 · On its way',
                'title' => 'How to send a gift',
                'subtitle' => 'Buy something for yourself, claim an item from someone\'s wishlist, or send a fully wrapped surprise — all in one checkout.',
                'steps' => [
                    ['title' => 'Browse or open a wishlist', 'description' => 'Shop our curated catalogue, or open a friend\'s wishlist link to see exactly what they\'d love.', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ['title' => 'Add to cart', 'description' => 'Add one or more items — claiming a wishlist item reserves it instantly so nobody else duplicates it.', 'icon' => 'M12 4v16m8-8H4'],
                    ['title' => 'Choose "Send as a gift"', 'description' => 'At checkout, ship to yourself or send it straight to the recipient — you don\'t need their address, just pick gift mode.', 'icon' => 'M12 8c-1.657 0-3 1.343-3 3 0 1.657 3 5 3 5s3-3.343 3-5c0-1.657-1.343-3-3-3z'],
                    ['title' => 'Personalise the packaging', 'description' => 'Pick a box or bag style, add a message sticker on the outside, or slip in a custom card — each with a live preview.', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
                    ['title' => 'Track it in Orders', 'description' => 'Follow every gift you\'ve sent or received from your Orders page, right up to delivery.', 'icon' => 'M20 7L12 3 4 7m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ],
                'cta' => ['label' => 'Browse the shop', 'route' => 'public.shop'],
                'secondaryCta' => ['label' => 'View my orders', 'route' => 'orders.index'],
            ],

            'website' => [
                'slug' => 'website',
                'emoji' => '🌐',
                'gradient' => 'from-fuchsia-500 to-pink-600',
                'mockupLabel' => 'giftloft.app/e/our-event',
                'title' => 'How to create your event website',
                'subtitle' => 'A full shareable page with your story, schedule, venue and wishlist — live in minutes, no code required.',
                'steps' => [
                    ['title' => 'Create your event', 'description' => 'Tell us the basics — event type, date, and where it\'s happening.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['title' => 'Pick a template', 'description' => 'Choose from designer templates — Classic, Modern, Festive, Botanical, Midnight or Storybook — each fully live-previewed.', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
                    ['title' => 'Add your story & schedule', 'description' => 'Open the Design editor to add your tagline, hosts, timeline, FAQs and photos.', 'icon' => 'M12 4v16m8-8H4'],
                    ['title' => 'Add your venue', 'description' => 'Upload a venue photo, drop a Google Maps link, and describe how to get there and where to stay.', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['title' => 'Publish & share', 'description' => 'Your page goes live at a shareable link instantly — post it anywhere or send it directly to guests.', 'icon' => 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757'],
                ],
                'cta' => ['label' => 'Create an event', 'route' => 'events.create'],
                'secondaryCta' => ['label' => 'Browse templates', 'route' => 'public.templates'],
            ],

            'invitation' => [
                'slug' => 'invitation',
                'emoji' => '💌',
                'gradient' => 'from-violet-500 to-purple-700',
                'mockupLabel' => 'giftloft.app/e/our-event/invitation',
                'title' => 'How to create your invitation',
                'subtitle' => 'A beautiful, shareable invitation card — perfect for WhatsApp, email or print — designed alongside your event website.',
                'steps' => [
                    ['title' => 'Create your event', 'description' => 'Invitations are generated from the same event you\'re already planning — no separate setup.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['title' => 'Choose a card design', 'description' => 'Pick from Elegant, Floral, Bold, Minimalist, Art Deco or Confetti — styled independently from your website.', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
                    ['title' => 'Personalise the wording', 'description' => 'Your event\'s title, hosts, date and venue automatically fill the card — edit any of it from the Design editor.', 'icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z'],
                    ['title' => 'Share the card', 'description' => 'Send the invitation link directly, or download/share it as a standalone card for WhatsApp and email.', 'icon' => 'M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z'],
                ],
                'cta' => ['label' => 'Create an event', 'route' => 'events.create'],
                'secondaryCta' => ['label' => 'Browse templates', 'route' => 'public.templates'],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}
