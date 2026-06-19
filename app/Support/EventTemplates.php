<?php

namespace App\Support;

/**
 * Catalog of available event-website and invitation templates.
 *
 * This is the single source of truth shared by validation (valid keys),
 * the design editor (gallery), and the public renderer (which maps the
 * stored key to a Vue component). Add a template here + a matching Vue
 * component under resources/js/Templates/* and it becomes selectable.
 */
class EventTemplates
{
    /**
     * Full-page event website templates.
     *
     * @return array<int, array<string, string>>
     */
    public static function websites(): array
    {
        return [
            ['key' => 'classic',   'name' => 'Classic Elegance',    'description' => 'Timeless serif design with a full-bleed cover and countdown.', 'accent' => 'indigo'],
            ['key' => 'modern',    'name' => 'Modern Minimal',      'description' => 'Clean, airy layout with bold typography and lots of white space.', 'accent' => 'neutral'],
            ['key' => 'festive',   'name' => 'Festive Celebration', 'description' => 'Vibrant, colourful and playful — perfect for parties.', 'accent' => 'rose'],
            ['key' => 'botanical', 'name' => 'Botanical Garden',    'description' => 'Soft cream and greenery for an organic, natural feel.', 'accent' => 'emerald'],
            ['key' => 'midnight',  'name' => 'Midnight Luxe',       'description' => 'Dramatic dark canvas with gold accents for evening affairs.', 'accent' => 'amber'],
            ['key' => 'storybook', 'name' => 'Storybook',           'description' => 'Warm scrapbook style with taped photos and handwritten charm.', 'accent' => 'rose'],
        ];
    }

    /**
     * Shareable invitation card templates.
     *
     * @return array<int, array<string, string>>
     */
    public static function invitations(): array
    {
        return [
            ['key' => 'elegant',     'name' => 'Elegant',     'description' => 'Refined serif card with gold accents.', 'accent' => 'amber'],
            ['key' => 'floral',      'name' => 'Floral',      'description' => 'Soft botanical card in blush tones.', 'accent' => 'rose'],
            ['key' => 'bold',        'name' => 'Bold',        'description' => 'High-contrast colour-block modern card.', 'accent' => 'violet'],
            ['key' => 'minimalist',  'name' => 'Minimalist',  'description' => 'Ultra-clean type-only card with airy spacing.', 'accent' => 'neutral'],
            ['key' => 'deco',        'name' => 'Art Deco',    'description' => 'Navy and gold geometric 1920s glamour.', 'accent' => 'amber'],
            ['key' => 'confetti',    'name' => 'Confetti',    'description' => 'Bright, playful party card with confetti.', 'accent' => 'fuchsia'],
        ];
    }

    /**
     * A realistic dummy event used to render template previews for customers.
     *
     * @return array<string, mixed>
     */
    public static function sampleEvent(string $template = 'classic', string $invitationTemplate = 'elegant'): array
    {
        return [
            'id' => 0,
            'title' => 'Sarah & James',
            'type' => 'wedding',
            'description' => "From a chance meeting in a little coffee shop to forever — we can't wait to celebrate our love story surrounded by the people who mean the most to us. Join us for an evening of love, laughter, and happily ever after.",
            'location' => 'Mumbai, India',
            'venue' => 'The Taj Mahal Palace',
            'cover_photo_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600&q=80',
            'starts_at' => now()->addDays(72)->setTime(16, 0)->toIso8601String(),
            'ends_at' => now()->addDays(72)->setTime(23, 0)->toIso8601String(),
            'status' => 'published',
            'is_public' => true,
            'share_code' => 'preview',
            'template' => $template,
            'invitation_template' => $invitationTemplate,
            'template_data' => [
                'hosts' => 'Sarah & James',
                'tagline' => "We're getting married!",
                'dress_code' => 'Garden formal',
                'rsvp_note' => 'Your presence is the greatest gift — but if you wish to give more, our registry is here.',
                'schedule' => [
                    ['time' => '4:00 PM', 'title' => 'Ceremony', 'detail' => 'Rose Garden Lawn'],
                    ['time' => '5:30 PM', 'title' => 'Cocktails', 'detail' => 'Terrace'],
                    ['time' => '7:00 PM', 'title' => 'Reception & Dinner', 'detail' => 'Grand Ballroom'],
                    ['time' => '9:00 PM', 'title' => 'Dancing', 'detail' => 'Till late!'],
                ],
                'faqs' => [
                    ['question' => 'Is there parking at the venue?', 'answer' => 'Yes — complimentary valet parking is available for all guests.'],
                    ['question' => 'Can I bring a plus one?', 'answer' => 'Please refer to your invitation for the number of seats reserved for you.'],
                    ['question' => 'Are children welcome?', 'answer' => 'We love your little ones, but this will be an adults-only celebration.'],
                ],
            ],
            'user' => ['id' => 0, 'name' => 'Sarah & James'],
            'wishlists' => [['id' => 0, 'name' => 'Our Registry', 'slug' => 'preview']],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function websiteKeys(): array
    {
        return array_column(self::websites(), 'key');
    }

    /**
     * @return array<int, string>
     */
    public static function invitationKeys(): array
    {
        return array_column(self::invitations(), 'key');
    }
}
