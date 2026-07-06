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
            ['key' => 'aurora',    'name' => 'Aurora Nights',       'description' => 'Northern-lights ribbons, glass cards and kinetic type under the stars.', 'accent' => 'sky'],
            ['key' => 'cinema',    'name' => 'Cinéma',              'description' => 'Black-and-white editorial with a pinned horizontal photo reel.', 'accent' => 'neutral'],
            ['key' => 'royal',     'name' => 'Royal Heritage',      'description' => 'Emerald and gold grandeur — a hand-drawn crest and arched gallery frames.', 'accent' => 'emerald'],
            ['key' => 'prism',     'name' => 'Prism',               'description' => 'Light glassmorphism with floating colour and a playful bento layout.', 'accent' => 'violet'],
            ['key' => 'ember',     'name' => 'Golden Hour',         'description' => 'Sun-washed terracotta warmth with counter-scrolling film-strip galleries.', 'accent' => 'orange'],
            ['key' => 'nova',      'name' => 'Nova',                'description' => 'Futuristic launch sequence — the title flies in, then your photo grows from a single dot to fill the screen.', 'accent' => 'sky'],
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
            ['key' => 'aurora',      'name' => 'Aurora',      'description' => 'Holographic night-sky card that tilts with your cursor.', 'accent' => 'sky'],
            ['key' => 'royal',       'name' => 'Royal',       'description' => 'Emerald and gold monogram card with a laurel crest.', 'accent' => 'emerald'],
            ['key' => 'noir',        'name' => 'Noir',        'description' => 'High-fashion editorial card with oversized date numerals.', 'accent' => 'neutral'],
            ['key' => 'vintage',     'name' => 'Vintage Seal', 'description' => 'Aged parchment finished with a pressed wax seal.', 'accent' => 'amber'],
            ['key' => 'neon',        'name' => 'Neon Glow',   'description' => 'Electric after-dark card that literally glows.', 'accent' => 'fuchsia'],
        ];
    }

    /**
     * A realistic dummy event used to render template previews for customers.
     *
     * @return array<string, mixed>
     */
    public static function sampleEvent(string $template = 'classic', string $invitationTemplate = 'elegant', string $type = 'wedding'): array
    {
        $samples = self::typeSamples();
        $s = $samples[$type] ?? $samples['wedding'];
        $photos = self::typePhotos()[$type] ?? self::typePhotos()['wedding'];

        return [
            'id' => 0,
            'title' => $s['title'],
            'type' => $type,
            'description' => $s['description'],
            'location' => $s['location'],
            'venue' => $s['venue'],
            'cover_photo_url' => $photos[0],
            'photos' => array_slice($photos, 1),
            'starts_at' => now()->addDays(60)->setTime(16, 0)->toIso8601String(),
            'ends_at' => now()->addDays(60)->setTime(23, 0)->toIso8601String(),
            'status' => 'published',
            'is_public' => true,
            'share_code' => 'preview',
            'template' => $template,
            'invitation_template' => $invitationTemplate,
            'template_data' => [
                'hosts' => $s['hosts'],
                'tagline' => $s['tagline'],
                'dress_code' => $s['dress_code'],
                'rsvp_note' => $s['rsvp_note'],
                'venue_note' => $s['venue_note'] ?? '',
                'travel' => $s['travel'] ?? '',
                'stay' => $s['stay'] ?? '',
                'schedule' => $s['schedule'],
                'faqs' => $s['faqs'],
            ],
            'user' => ['id' => 0, 'name' => $s['hosts']],
            'wishlists' => [['id' => 0, 'name' => 'Our Wishlist', 'slug' => 'preview']],
        ];
    }

    /**
     * One themed sample event per event type, keyed by type.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function allSamples(string $template = 'classic', string $invitationTemplate = 'elegant'): array
    {
        $out = [];
        foreach (array_keys(self::typeSamples()) as $type) {
            $out[$type] = self::sampleEvent($template, $invitationTemplate, $type);
        }

        return $out;
    }

    /**
     * Display labels for each supported event type (preview filter).
     *
     * @return array<string, string>
     */
    public static function eventTypeLabels(): array
    {
        return [
            'wedding' => 'Wedding',
            'birthday' => 'Birthday',
            'engagement' => 'Engagement',
            'anniversary' => 'Anniversary',
            'baby_shower' => 'Baby Shower',
            'proposal' => 'Proposal',
            'graduation' => 'Graduation',
            'housewarming' => 'Housewarming',
        ];
    }

    /**
     * Curated Unsplash photography per event type — first entry is the cover,
     * the rest fill the gallery. Templates gracefully fall back to gradient
     * heroes if a photo can't load (offline dev, removed image, …).
     *
     * @return array<string, array<int, string>>
     */
    protected static function typePhotos(): array
    {
        $u = fn (string $id, int $w = 1600) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q=80";

        return [
            'wedding' => [
                $u('1519741497674-611481863552'), // couple portrait
                $u('1511285560929-80b456fea0bc', 1200),
                $u('1465495976277-4387d4b0b4c6', 1200),
                $u('1522673607200-164d1b6ce486', 1200),
                $u('1469371670807-013ccf25f16a', 1200),
            ],
            'birthday' => [
                $u('1530103862676-de8c9debad1d'), // balloons
                $u('1464349095431-e9a21285b5f3', 1200),
                $u('1513151233558-d860c5398176', 1200),
                $u('1492684223066-81342ee5ff30', 1200),
                $u('1543269865-cbf427effbad', 1200),
            ],
            'engagement' => [
                $u('1529634806980-85c3dd6d34ac'), // proposal moment
                $u('1516589178581-6cd7833ae3b2', 1200),
                $u('1519225421980-715cb0215aed', 1200),
                $u('1492684223066-81342ee5ff30', 1200),
                $u('1469371670807-013ccf25f16a', 1200),
            ],
            'anniversary' => [
                $u('1414235077428-338989a2e8c0'), // fine dining
                $u('1529333166437-7750a6dd5a70', 1200),
                $u('1470337458703-46ad1756a187', 1200),
                $u('1543269865-cbf427effbad', 1200),
                $u('1519225421980-715cb0215aed', 1200),
            ],
            'baby_shower' => [
                $u('1519689680058-324335c77eba'), // baby feet
                $u('1555252333-9f8e92e65df9', 1200),
                $u('1530103862676-de8c9debad1d', 1200),
                $u('1513151233558-d860c5398176', 1200),
            ],
            'proposal' => [
                $u('1529634806980-85c3dd6d34ac'), // the moment
                $u('1516589178581-6cd7833ae3b2', 1200),
                $u('1492684223066-81342ee5ff30', 1200),
                $u('1522673607200-164d1b6ce486', 1200),
            ],
            'graduation' => [
                $u('1541339907198-e08756dedf3f'), // campus walk
                $u('1492684223066-81342ee5ff30', 1200),
                $u('1529333166437-7750a6dd5a70', 1200),
                $u('1543269865-cbf427effbad', 1200),
            ],
            'housewarming' => [
                $u('1484154218962-a197022b5858'), // warm kitchen
                $u('1522708323590-d24dbb6b0267', 1200),
                $u('1493809842364-78817add7ffb', 1200),
                $u('1513694203232-719a280e022f', 1200),
            ],
        ];
    }

    /**
     * Themed dummy content per event type.
     *
     * @return array<string, array<string, mixed>>
     */
    protected static function typeSamples(): array
    {
        return [
            'wedding' => [
                'hosts' => 'Sarah & James', 'title' => 'Sarah & James', 'tagline' => "We're getting married!",
                'location' => 'Mumbai, India', 'venue' => 'The Taj Mahal Palace', 'dress_code' => 'Garden formal',
                'description' => "From a chance meeting in a little coffee shop to forever — we can't wait to celebrate our love story with the people who mean the most to us.",
                'rsvp_note' => 'Your presence is the greatest gift — but if you wish to give more, our wishlist is here.',
                'venue_note' => 'A sea-facing heritage ballroom beneath crystal chandeliers, steps from the Gateway of India. The ceremony unfolds on the Rose Garden Lawn as the sun sets over the Arabian Sea.',
                'travel' => '20 minutes from CSMIA airport. Complimentary valet at the Palm Court entrance, and a shuttle runs from the Colaba causeway every half hour.',
                'stay' => 'A room block is reserved at the Taj (mention “Sarah & James”). Boutique stays at Abode and The Bombay Canteen quarter are five minutes away.',
                'schedule' => [
                    ['time' => '4:00 PM', 'title' => 'Ceremony', 'detail' => 'Rose Garden Lawn'],
                    ['time' => '5:30 PM', 'title' => 'Cocktails', 'detail' => 'Terrace'],
                    ['time' => '7:00 PM', 'title' => 'Reception & Dinner', 'detail' => 'Grand Ballroom'],
                ],
                'faqs' => [
                    ['question' => 'Is there parking?', 'answer' => 'Yes — complimentary valet parking for all guests.'],
                    ['question' => 'Are children welcome?', 'answer' => 'We love them, but this is an adults-only celebration.'],
                ],
            ],
            'birthday' => [
                'hosts' => "Maya's 30th", 'title' => 'Maya turns 30!', 'tagline' => "Let's party! 🎉",
                'location' => 'Bandra, Mumbai', 'venue' => 'The Rooftop Lounge', 'dress_code' => 'Smart casual',
                'description' => "Three decades of fabulous and just getting started! Join Maya for an unforgettable night of music, cake, and dancing.",
                'rsvp_note' => 'No gifts necessary — but if you insist, peek at the wishlist!',
                'venue_note' => 'An open-air rooftop with skyline views, fairy lights, and a dance floor under the stars. The bar opens at seven sharp.',
                'travel' => 'Linking Road metro is a five-minute walk; ride-shares drop right at the tower lobby. Take the lift to level 21.',
                'stay' => 'Crashing after the party? The Executive Inn next door has late checkout for guests — ask for the Maya30 rate.',
                'schedule' => [
                    ['time' => '7:00 PM', 'title' => 'Welcome drinks', 'detail' => 'Rooftop bar'],
                    ['time' => '8:30 PM', 'title' => 'Cake & toast', 'detail' => ''],
                    ['time' => '9:00 PM', 'title' => 'DJ & dancing', 'detail' => 'Till late'],
                ],
                'faqs' => [
                    ['question' => 'Can I bring a friend?', 'answer' => 'Absolutely — the more the merrier!'],
                ],
            ],
            'engagement' => [
                'hosts' => 'Priya & Arjun', 'title' => 'Priya & Arjun', 'tagline' => 'She said yes! 💍',
                'location' => 'Delhi, India', 'venue' => 'The Garden Pavilion', 'dress_code' => 'Cocktail',
                'description' => "We're engaged and over the moon! Come raise a glass with us as we begin this beautiful new chapter together.",
                'rsvp_note' => 'Your blessings mean the world. A wishlist is available if you wish.',
                'venue_note' => 'A glasshouse pavilion wrapped in gardens — marigold installations, a live sitar duo, and the ring ceremony under the old banyan tree.',
                'travel' => '30 minutes from IGI airport via NH48. On-site parking for 200 cars; e-rickshaws ferry guests from the gate to the pavilion.',
                'stay' => 'Preferred rates at The Leela Ambience (code PRIYA-ARJUN) and a curated Airbnb list for the weekend is on request.',
                'schedule' => [
                    ['time' => '6:30 PM', 'title' => 'Welcome', 'detail' => 'Garden Pavilion'],
                    ['time' => '7:30 PM', 'title' => 'Ring ceremony', 'detail' => ''],
                    ['time' => '8:30 PM', 'title' => 'Dinner & celebration', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'What should I wear?', 'answer' => 'Cocktail attire — dress to impress!'],
                ],
            ],
            'anniversary' => [
                'hosts' => 'Anil & Meera', 'title' => '25 Wonderful Years', 'tagline' => 'Celebrating our silver jubilee',
                'location' => 'Bengaluru, India', 'venue' => 'The Heritage Club', 'dress_code' => 'Formal',
                'description' => "Twenty-five years, countless memories, and a love that keeps growing. Join us as we celebrate this special milestone.",
                'rsvp_note' => 'Your company is all we ask for on our special day.',
                'venue_note' => 'The colonial-era Heritage Club — teak floors, slow ceiling fans, and the long verandah where we had our first dance in 2001.',
                'travel' => '15 minutes from MG Road metro. The club offers valet; autos and cabs drop at the porte-cochère.',
                'stay' => 'Out-of-town guests: the club has six heritage rooms on hold for us, and the Oberoi is a short drive away.',
                'schedule' => [
                    ['time' => '7:00 PM', 'title' => 'Reception', 'detail' => 'Heritage Club'],
                    ['time' => '8:00 PM', 'title' => 'Dinner & speeches', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'Is dinner provided?', 'answer' => 'Yes, a full plated dinner will be served.'],
                ],
            ],
            'baby_shower' => [
                'hosts' => 'Baby Kapoor', 'title' => 'A Little One Is On the Way', 'tagline' => "It's almost time! 👶",
                'location' => 'Pune, India', 'venue' => 'The Sunroom Café', 'dress_code' => 'Soft pastels',
                'description' => "We're welcoming a tiny new member to our family and would love for you to shower us with joy, advice, and a little fun.",
                'rsvp_note' => 'Gifts from our wishlist help us prepare for the big arrival.',
                'venue_note' => 'A plant-filled sunroom café with soft corners, bottomless mocktails, and a long brunch table set in the bay window.',
                'travel' => 'Koregaon Park, lane 5 — parking in the rear courtyard, strollers welcome via the garden ramp.',
                'stay' => 'Visiting family can book the café’s partner guesthouse two doors down — cots available on request.',
                'schedule' => [
                    ['time' => '11:00 AM', 'title' => 'Brunch', 'detail' => 'Sunroom Café'],
                    ['time' => '12:30 PM', 'title' => 'Games & gifts', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'Boy or girl?', 'answer' => "It's a surprise — come find out with us!"],
                ],
            ],
            'proposal' => [
                'hosts' => 'Will you marry me?', 'title' => 'A Moment to Remember', 'tagline' => 'The big question 💜',
                'location' => 'Goa, India', 'venue' => 'Sunset Beach', 'dress_code' => 'Beach elegant',
                'description' => "Some moments change everything. Be part of a surprise that turns into a forever memory.",
                'rsvp_note' => 'Shhh — it’s a surprise. Your discretion is the best gift.',
                'venue_note' => 'A private stretch of Sunset Beach — a lantern-lit path through the palms leads to a circle of frangipani and one very important question.',
                'travel' => '25 minutes from Dabolim airport. Park at the Dunes shack; a boardwalk (and a golf cart, if needed) covers the last 200 metres.',
                'stay' => 'We’ve held a floor at the beach resort next door so the celebration can continue at sunrise.',
                'schedule' => [
                    ['time' => '5:30 PM', 'title' => 'Golden hour', 'detail' => 'Sunset Beach'],
                    ['time' => '6:00 PM', 'title' => 'The moment', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'When should I arrive?', 'answer' => 'Please be seated by 5:15 PM sharp.'],
                ],
            ],
            'graduation' => [
                'hosts' => 'Riya · Class of 2026', 'title' => 'We Did It! 🎓', 'tagline' => 'Caps off to the graduate',
                'location' => 'Chennai, India', 'venue' => 'The University Hall', 'dress_code' => 'Smart casual',
                'description' => "Years of hard work have paid off! Join us to celebrate this proud achievement and toast to bright beginnings.",
                'rsvp_note' => 'Your encouragement has meant everything along the way.',
                'venue_note' => 'The University Hall — stone arches, banners of every graduating class since 1954, and the lawn where the caps go flying.',
                'travel' => 'Campus shuttle from Chennai Central every 20 minutes; visitor parking at Gate 3 with a short walk along the avenue of rain trees.',
                'stay' => 'The campus guesthouse and the Residency on College Road both hold rooms for graduation weekend.',
                'schedule' => [
                    ['time' => '5:00 PM', 'title' => 'Ceremony', 'detail' => 'University Hall'],
                    ['time' => '7:00 PM', 'title' => 'Celebration dinner', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'Can family attend?', 'answer' => 'Yes, all are warmly welcome.'],
                ],
            ],
            'housewarming' => [
                'hosts' => 'The Sharmas', 'title' => 'Our New Home', 'tagline' => 'Come see our new place! 🏡',
                'location' => 'Hyderabad, India', 'venue' => '12 Lotus Avenue', 'dress_code' => 'Casual',
                'description' => "We've finally found our forever home and can't wait to share it with you. Drop by for warmth, food, and good company.",
                'rsvp_note' => 'No gifts needed — just bring yourselves! A wishlist is here if you’d like.',
                'venue_note' => 'Our new place on Lotus Avenue — a skylit courtyard house with a mango tree in the middle and far too many bookshelves already.',
                'travel' => 'Ten minutes from Jubilee Hills checkpost; street parking along the avenue and two EV chargers at the community lot.',
                'stay' => 'The guest room is claimed (sorry!), but the Treebo around the corner is lovely and walkable.',
                'schedule' => [
                    ['time' => '12:00 PM', 'title' => 'Open house', 'detail' => '12 Lotus Avenue'],
                    ['time' => '1:00 PM', 'title' => 'Lunch', 'detail' => ''],
                ],
                'faqs' => [
                    ['question' => 'Is parking available?', 'answer' => 'Yes, street parking is plentiful.'],
                ],
            ],
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
