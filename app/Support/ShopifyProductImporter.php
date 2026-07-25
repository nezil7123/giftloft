<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Imports a Shopify "Products" CSV export (Products → Export → All products
 * → CSV, from the Shopify admin) into the Product catalog.
 *
 * Shopify writes one row per variant/image, repeating the Handle and leaving
 * Title/Body/Type blank after the first row of each product — so rows are
 * grouped by Handle and only the first non-empty value per column is used.
 * Gift Loft's catalog has no variants, so only the first price/image per
 * product is kept.
 */
class ShopifyProductImporter
{
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
     * @return array{created: int, skipped_duplicate: int, skipped_invalid: int}
     */
    public function import(string $path, string $defaultCategory, ?string $defaultGender): array
    {
        $rows = $this->readCsv($path);
        if (empty($rows)) {
            return ['created' => 0, 'skipped_duplicate' => 0, 'skipped_invalid' => 0];
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
            $imageUrl = $this->firstNonEmpty($records, 'Image Src') ?? $this->firstNonEmpty($records, 'Variant Image');
            $category = $this->guessCategory($records) ?? $defaultCategory;
            $sortOrder++;

            Product::create([
                'name' => Str::limit($name, 150, ''),
                'slug' => $slug,
                'description' => $description ? Str::limit(trim(strip_tags($description)), 1000, '') : null,
                'category' => $category,
                'gender' => $defaultGender,
                'price' => round((float) $price, 2),
                'image_url' => $imageUrl ?: null,
                'product_url' => null,
                'emoji' => '🎁',
                'accent' => self::ACCENTS[$accentIndex++ % count(self::ACCENTS)],
                'is_active' => $this->guessActive($records),
                'sort_order' => $sortOrder,
            ]);
            $created++;
        }

        return ['created' => $created, 'skipped_duplicate' => $skippedDuplicate, 'skipped_invalid' => $skippedInvalid];
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
