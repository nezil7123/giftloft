<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imports a Shopify "Products" CSV export (Products → Export → All products
 * → CSV, from the Shopify admin) into the Product catalog.
 *
 * Shopify writes one row per variant/image, repeating the Handle and leaving
 * Title/Body/Type blank after the first row of each product — so rows are
 * grouped by Handle and only the first non-empty value per column is used.
 * Gift Loft's catalog has no variants, so only the first price is kept, but
 * every distinct "Image Src" row for the product is imported as a gallery
 * image (ordered by "Image Position" when present).
 *
 * Every image is downloaded and re-hosted on our own storage disk rather
 * than hotlinked from Shopify's CDN, so it keeps working even after the
 * Shopify store is closed. If a download fails, the original Shopify URL is
 * kept as a fallback rather than losing the image entirely.
 */
class ShopifyProductImporter
{
    private const IMAGE_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    private const MAX_IMAGE_BYTES = 8 * 1024 * 1024; // matches EventPhotoController's per-photo limit
    private const CATEGORY_KEYWORDS = [
        'wedding' => ['wedding', 'bridal'],
        'birthday' => ['birthday', 'party'],
        'baby' => ['baby', 'infant', 'newborn', 'nursery'],
        'home' => ['home', 'living', 'kitchen', 'decor', 'furniture'],
        'experiences' => ['experience', 'activity', 'voucher', 'ticket'],
        'fashion' => ['fashion', 'apparel', 'clothing', 'wear', 'shoe'],
        'jewellery' => ['jewellery', 'jewelry', 'ring', 'necklace', 'earring', 'bracelet'],
        'gadgets' => ['gadget', 'electronic', 'tech', 'device'],
    ];

    private const ACCENTS = ['indigo', 'rose', 'amber', 'violet', 'emerald', 'sky', 'neutral'];

    /**
     * @return array{created: int, skipped_duplicate: int, skipped_invalid: int, images_hotlinked: int}
     */
    public function import(string $path, string $defaultCategory, ?string $defaultGender): array
    {
        // A catalog with many products — and now every image per product,
        // not just one — means many synchronous downloads below; the
        // default PHP time limit can be far too short for that.
        set_time_limit(600);

        $rows = $this->readCsv($path);
        if (empty($rows)) {
            return ['created' => 0, 'skipped_duplicate' => 0, 'skipped_invalid' => 0, 'images_hotlinked' => 0];
        }

        $header = array_map('trim', array_shift($rows));
        $groups = [];
        foreach ($rows as $row) {
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }
            $record = array_combine($header, array_slice($row, 0, count($header)));
            $handle = trim($record['Handle'] ?? '');
            if ($handle === '') {
                continue;
            }
            $groups[$handle][] = $record;
        }

        $created = 0;
        $skippedDuplicate = 0;
        $skippedInvalid = 0;
        $imagesHotlinked = 0;
        $sortOrder = (int) (Product::max('sort_order') ?? 0);
        $accentIndex = 0;

        foreach ($groups as $handle => $records) {
            $name = $this->firstNonEmpty($records, 'Title');
            $price = $this->firstNonEmpty($records, 'Variant Price');

            if ($name === null || $price === null || ! is_numeric($price)) {
                $skippedInvalid++;
                continue;
            }

            $slug = Str::slug($handle) ?: Str::slug($name);
            if ($slug === '' || Product::where('slug', $slug)->exists()) {
                $skippedDuplicate++;
                continue;
            }

            $description = $this->firstNonEmpty($records, 'Body (HTML)');
            $category = $this->guessCategory($records) ?? $defaultCategory;
            $sortOrder++;

            $images = [];
            foreach ($this->collectImageUrls($records) as $sourceUrl) {
                $rehosted = $this->rehostImage($sourceUrl);
                if ($rehosted === $sourceUrl) {
                    $imagesHotlinked++;
                }
                $images[] = $rehosted;
            }
            $images = array_values(array_unique($images));

            Product::create([
                'name' => Str::limit($name, 150, ''),
                'slug' => $slug,
                'description' => $description ? Str::limit(trim(strip_tags($description)), 1000, '') : null,
                'category' => $category,
                'gender' => $defaultGender,
                'price' => round((float) $price, 2),
                'image_url' => $images[0] ?? null,
                'images' => $images ?: null,
                'product_url' => null,
                'emoji' => '🎁',
                'accent' => self::ACCENTS[$accentIndex++ % count(self::ACCENTS)],
                'is_active' => $this->guessActive($records),
                'sort_order' => $sortOrder,
            ]);
            $created++;
        }

        return [
            'created' => $created,
            'skipped_duplicate' => $skippedDuplicate,
            'skipped_invalid' => $skippedInvalid,
            'images_hotlinked' => $imagesHotlinked,
        ];
    }

    /**
     * Download a Shopify image and store it on our own public disk so it
     * survives the Shopify store closing. Falls back to the original URL
     * (still hotlinked) if the download fails or isn't a recognizable image.
     */
    private function rehostImage(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        try {
            $response = Http::timeout(8)->get($url);
        } catch (\Throwable) {
            return $url;
        }

        if (! $response->successful()) {
            return $url;
        }

        $body = $response->body();
        if ($body === '' || strlen($body) > self::MAX_IMAGE_BYTES) {
            return $url;
        }

        $extension = $this->extensionFor($url, $response->header('Content-Type'));
        if ($extension === null) {
            return $url;
        }

        $filename = Str::random(24).'.'.$extension;
        Storage::disk('public')->put("products/{$filename}", $body);

        return Storage::disk('public')->url("products/{$filename}");
    }

    private function extensionFor(string $url, ?string $contentType): ?string
    {
        if ($contentType) {
            $mime = strtolower(trim(explode(';', $contentType)[0]));
            if (isset(self::IMAGE_EXTENSIONS[$mime])) {
                return self::IMAGE_EXTENSIONS[$mime];
            }
        }

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return in_array($ext, ['jpg', 'png', 'webp', 'gif'], true) ? $ext : null;
    }

    /**
     * @return list<list<string>>
     */
    private function readCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }
        // Shopify exports UTF-8 with a BOM; strip it so the "Handle" header matches.
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @param list<array<string, string>> $records
     */
    private function firstNonEmpty(array $records, string $column): ?string
    {
        foreach ($records as $record) {
            $value = trim($record[$column] ?? '');
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * All distinct image URLs for a product, ordered by Shopify's "Image
     * Position" column when present (falling back to CSV row order), then
     * deduplicated. Falls back to "Variant Image" values when the product
     * has no dedicated "Image Src" rows at all.
     *
     * @param list<array<string, string>> $records
     * @return list<string>
     */
    private function collectImageUrls(array $records): array
    {
        $ordered = [];
        foreach ($records as $i => $record) {
            $src = trim($record['Image Src'] ?? '');
            if ($src === '') {
                continue;
            }
            $position = is_numeric($record['Image Position'] ?? null) ? (float) $record['Image Position'] : $i;
            $ordered[] = [$position, $src];
        }

        if (empty($ordered)) {
            foreach ($records as $i => $record) {
                $src = trim($record['Variant Image'] ?? '');
                if ($src !== '') {
                    $ordered[] = [$i, $src];
                }
            }
        }

        usort($ordered, fn ($a, $b) => $a[0] <=> $b[0]);

        $urls = [];
        foreach ($ordered as [, $src]) {
            if (! in_array($src, $urls, true)) {
                $urls[] = $src;
            }
        }

        return $urls;
    }

    /**
     * @param list<array<string, string>> $records
     */
    private function guessCategory(array $records): ?string
    {
        $haystack = strtolower(
            ($this->firstNonEmpty($records, 'Product Category') ?? '').' '.
            ($this->firstNonEmpty($records, 'Type') ?? '').' '.
            ($this->firstNonEmpty($records, 'Tags') ?? '')
        );
        if ($haystack === '') {
            return null;
        }

        foreach (self::CATEGORY_KEYWORDS as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * @param list<array<string, string>> $records
     */
    private function guessActive(array $records): bool
    {
        $status = $this->firstNonEmpty($records, 'Status');
        if ($status !== null) {
            return strtolower($status) === 'active';
        }

        $published = $this->firstNonEmpty($records, 'Published');
        if ($published !== null) {
            return strtolower($published) === 'true';
        }

        return true;
    }
}
