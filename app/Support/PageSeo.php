<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Server-rendered SEO + social sharing metadata.
 *
 * Inertia's <Head> component only runs in the browser, and the crawlers that
 * build link previews — Facebook, WhatsApp, X/Twitter, LinkedIn, Slack,
 * iMessage — do not execute JavaScript. So every meta tag is resolved here and
 * printed into the initial HTML by partials/seo.blade.php instead.
 *
 * Keeping the whole map in one file means a page's title and description live
 * next to each other rather than being scattered across ten controllers.
 */
class PageSeo
{
    public const DEFAULT_IMAGE = '/brand/comeyay-og.png';

    private const DEFAULT_TITLE = 'ComeYay — Free Event Websites & Digital Invitations';

    private const DEFAULT_DESCRIPTION = 'Create a free event website and digital invitations for your wedding, birthday, baby shower or party. 22 premium designs, schedule, travel details and photo gallery — live in minutes.';

    /**
     * Resolve the meta for an Inertia page.
     *
     * @param  array<string, mixed>  $props
     * @return array<string, mixed>
     */
    public static function for(?string $component, array $props = []): array
    {
        $meta = match ($component) {
            'Public/Landing' => [
                'title' => self::DEFAULT_TITLE,
                'description' => self::DEFAULT_DESCRIPTION,
            ],

            'Public/Templates/Index' => [
                'title' => 'Event Website & Invitation Templates',
                'description' => 'Browse 22 animated event website designs and 21 digital invitation cards for weddings, birthdays, baby showers and more. Preview any template free — no code required.',
            ],

            'Public/Templates/WebsitePreview' => self::templatePreview($props),

            'Help/Index' => [
                'title' => 'How ComeYay Works',
                'description' => 'Step-by-step guides for building your event website and sending digital invitations with ComeYay — from picking a design to sharing the link with your guests.',
            ],

            'Help/Guide' => [
                'title' => data_get($props, 'guide.title') ?: 'Guide',
                'description' => data_get($props, 'guide.subtitle') ?: self::DEFAULT_DESCRIPTION,
                'type' => 'article',
            ],

            'Public/Legal/Privacy' => [
                'title' => 'Privacy Policy',
                'description' => 'How ComeYay collects, uses and protects your personal information when you create an event website or send digital invitations.',
            ],

            'Public/Legal/Terms' => [
                'title' => 'Terms & Conditions',
                'description' => "The terms that govern your use of ComeYay's event website and digital invitation services.",
            ],

            // Share-code pages: kept out of search, but they are the links people
            // actually paste into chats, so they get the richest previews of all.
            'Public/EventShow' => self::event($props, false),
            'Public/EventInvitation' => self::event($props, true),

            'Public/WishlistShow' => [
                'title' => data_get($props, 'wishlist.name', 'Wishlist'),
                'description' => 'A wishlist on ComeYay.',
                'noindex' => true,
            ],

            // Everything else is the signed-in app: never indexed.
            default => [
                'title' => 'ComeYay',
                'description' => self::DEFAULT_DESCRIPTION,
                'noindex' => true,
            ],
        };

        return array_merge([
            'title' => self::DEFAULT_TITLE,
            'description' => self::DEFAULT_DESCRIPTION,
            'image' => self::DEFAULT_IMAGE,
            'imageAlt' => null,
            'type' => 'website',
            'noindex' => false,
        ], $meta);
    }

    /**
     * Titles read "<Page> — ComeYay" unless they already name the brand.
     */
    public static function fullTitle(string $title): string
    {
        return str_contains($title, 'ComeYay') ? $title : $title.' — ComeYay';
    }

    /**
     * Resolve an image path against the site root — crawlers reject relative URLs.
     */
    public static function absoluteImage(?string $path): string
    {
        $path = $path ?: self::DEFAULT_IMAGE;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
    }

    /**
     * @param  array<string, mixed>  $props
     * @return array<string, mixed>
     */
    private static function templatePreview(array $props): array
    {
        $key = (string) data_get($props, 'templateKey', '');
        $name = $key !== '' ? Str::headline($key) : 'Event website';

        return [
            'title' => $name.' — Event Website Template',
            'description' => "Preview the {$name} event website design on ComeYay — a free, fully animated template with a countdown, schedule, travel details and photo gallery.",
        ];
    }

    /**
     * Build the link preview for a public event or its invitation.
     *
     * @param  array<string, mixed>  $props
     * @return array<string, mixed>
     */
    private static function event(array $props, bool $isInvitation): array
    {
        $event = (array) data_get($props, 'event', []);
        $title = (string) data_get($event, 'title', 'Celebration');
        $data = (array) data_get($event, 'template_data', []);

        $hosts = trim((string) data_get($data, 'hosts', '')) ?: (string) data_get($event, 'user.name', '');
        $venue = trim((string) data_get($event, 'venue', ''));
        $place = $venue !== '' ? $venue : trim((string) data_get($event, 'location', ''));
        $occasion = self::occasion((string) data_get($event, 'type', ''));

        $date = '';
        if ($startsAt = data_get($event, 'starts_at')) {
            try {
                $date = Carbon::parse($startsAt)->format('j F Y');
            } catch (\Throwable) {
                $date = '';
            }
        }

        // Prefer the host's own words, then their description, then a sentence
        // assembled from whatever details they filled in.
        $tagline = trim((string) data_get($data, 'tagline', ''));
        $summary = trim((string) data_get($event, 'description', ''));
        $whenWhere = implode(' · ', array_filter([$date, $place]));

        if ($tagline !== '') {
            $description = implode(' — ', array_filter([$tagline, $whenWhere]));
        } elseif ($summary !== '') {
            $description = Str::limit($summary, 180);
        } elseif ($whenWhere !== '') {
            $when = $date !== '' ? " on {$date}" : '';
            $where = $place !== '' ? " at {$place}" : '';
            $description = $hosts !== ''
                ? "{$hosts} invite you to their {$occasion}{$when}{$where}."
                : "You're invited{$when}{$where}.";
        } else {
            $description = "You're invited — see all the details and plan your visit.";
        }

        $photos = array_values(array_filter((array) data_get($event, 'photos', [])));
        $image = data_get($event, 'cover_photo_url') ?: ($photos[0] ?? self::DEFAULT_IMAGE);

        return [
            'title' => $isInvitation ? "You're invited — {$title}" : $title,
            'description' => $description,
            'image' => $image,
            'imageAlt' => "{$title} — {$occasion}",
            'type' => 'article',
            'noindex' => true,
        ];
    }

    private static function occasion(string $type): string
    {
        return match ($type) {
            'wedding' => 'wedding',
            'birthday' => 'birthday party',
            'engagement' => 'engagement',
            'anniversary' => 'anniversary',
            'baby_shower' => 'baby shower',
            'proposal' => 'proposal',
            'graduation' => 'graduation',
            'housewarming' => 'housewarming',
            default => 'celebration',
        };
    }
}
