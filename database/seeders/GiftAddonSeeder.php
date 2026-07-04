<?php

namespace Database\Seeders;

use App\Models\GiftAddon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GiftAddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            // [type, name, description, price, is_default, sort_order, svgKey]
            ['packaging', 'Standard Box', 'Our regular sturdy shipping box.', 0, true, 0, 'box-standard'],
            ['packaging', 'Eco Kraft Box', 'Recycled kraft box with a rustic finish.', 99, false, 1, 'box-kraft'],
            ['packaging', 'Premium Gift Box', 'Rigid box with ribbon and tissue wrap.', 149, false, 2, 'box-premium'],
            ['packaging', 'Gift Bag', 'A reusable fabric gift bag.', 129, false, 3, 'box-bag'],

            ['message_sticker', 'Classic round sticker', 'A clean round sticker with your message, on the outside of the box.', 49, false, 0, 'sticker-classic'],
            ['message_sticker', 'Floral sticker', 'A round sticker framed with a delicate floral border.', 59, false, 1, 'sticker-floral'],
            ['message_sticker', 'Kraft label sticker', 'A rustic kraft-paper label tied with twine.', 55, false, 2, 'sticker-kraft'],

            ['custom_card', 'Classic greeting card', 'A simple printed greeting card with your message, tucked inside.', 79, false, 0, 'card-classic'],
            ['custom_card', 'Floral greeting card', 'A greeting card with a soft floral corner design.', 89, false, 1, 'card-floral'],
            ['custom_card', 'Photo frame card', 'A card with a picture-frame layout and your message below.', 95, false, 2, 'card-photo'],
        ];

        foreach ($addons as [$type, $name, $description, $price, $isDefault, $sortOrder, $svgKey]) {
            GiftAddon::updateOrCreate(
                ['type' => $type, 'name' => $name],
                [
                    'description' => $description,
                    'image' => $this->previewImage($svgKey),
                    'price' => $price,
                    'is_default' => $isDefault,
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ],
            );
        }
    }

    /**
     * Generate (or reuse) a simple SVG preview image for a gift addon template
     * so admins and buyers can see roughly how it looks without real product photography.
     */
    protected function previewImage(string $key): string
    {
        $path = "gift-addons/{$key}.svg";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->svg($key));
        }

        return "/storage/{$path}";
    }

    protected function svg(string $key): string
    {
        $body = match ($key) {
            'box-standard' => '
                <rect width="240" height="240" fill="#F5EFE3"/>
                <rect x="40" y="70" width="160" height="130" fill="#D8B98A" stroke="#B4926091" stroke-width="3"/>
                <polygon points="40,70 200,70 170,40 70,40" fill="#E7CCA0" stroke="#B49260" stroke-width="3"/>
                <line x1="120" y1="70" x2="120" y2="200" stroke="#B49260" stroke-width="3"/>',
            'box-kraft' => '
                <rect width="240" height="240" fill="#EFE6D6"/>
                <rect x="40" y="70" width="160" height="130" fill="#C7A470" stroke="#9C7A46" stroke-width="3"/>
                <polygon points="40,70 200,70 170,40 70,40" fill="#D8B98A" stroke="#9C7A46" stroke-width="3"/>
                <path d="M120 90 C100 110 100 130 120 150 C140 130 140 110 120 90 Z" fill="#6B8F5C"/>
                <path d="M120 90 L120 150" stroke="#4F6E44" stroke-width="2"/>',
            'box-premium' => '
                <rect width="240" height="240" fill="#F3F1EE"/>
                <rect x="35" y="65" width="170" height="140" fill="#2B2B2E"/>
                <rect x="108" y="65" width="24" height="140" fill="#D4AF37"/>
                <rect x="35" y="122" width="170" height="24" fill="#D4AF37"/>
                <circle cx="120" cy="134" r="16" fill="#2B2B2E" stroke="#D4AF37" stroke-width="3"/>',
            'box-bag' => '
                <rect width="240" height="240" fill="#FBF0F4"/>
                <polygon points="60,90 180,90 195,205 45,205" fill="#E7A6C4" stroke="#C97CA0" stroke-width="3"/>
                <path d="M85 90 C85 55 155 55 155 90" fill="none" stroke="#8A4E68" stroke-width="6"/>
                <rect x="95" y="130" width="50" height="36" fill="#FBF0F4" stroke="#C97CA0" stroke-width="3"/>',
            'sticker-classic' => '
                <rect width="240" height="240" fill="#F7F5F0"/>
                <circle cx="120" cy="120" r="85" fill="#FFFFFF" stroke="#C9C2B4" stroke-width="4" stroke-dasharray="6 6"/>
                <line x1="80" y1="108" x2="160" y2="108" stroke="#B7AF9E" stroke-width="5" stroke-linecap="round"/>
                <line x1="80" y1="128" x2="160" y2="128" stroke="#B7AF9E" stroke-width="5" stroke-linecap="round"/>
                <line x1="95" y1="148" x2="145" y2="148" stroke="#B7AF9E" stroke-width="5" stroke-linecap="round"/>',
            'sticker-floral' => '
                <rect width="240" height="240" fill="#FBF6F8"/>
                <circle cx="120" cy="120" r="85" fill="#FFFFFF" stroke="#E7A6C4" stroke-width="4"/>
                <g fill="#E7A6C4">
                    <circle cx="120" cy="45" r="10"/><circle cx="195" cy="120" r="10"/>
                    <circle cx="120" cy="195" r="10"/><circle cx="45" cy="120" r="10"/>
                </g>
                <line x1="85" y1="110" x2="155" y2="110" stroke="#C97CA0" stroke-width="5" stroke-linecap="round"/>
                <line x1="85" y1="130" x2="155" y2="130" stroke="#C97CA0" stroke-width="5" stroke-linecap="round"/>',
            'sticker-kraft' => '
                <rect width="240" height="240" fill="#F5EFE3"/>
                <rect x="40" y="80" width="160" height="90" fill="#D8B98A" stroke="#9C7A46" stroke-width="4"/>
                <line x1="120" y1="30" x2="120" y2="80" stroke="#9C7A46" stroke-width="3" stroke-dasharray="4 4"/>
                <line x1="60" y1="105" x2="180" y2="105" stroke="#7A5C33" stroke-width="5" stroke-linecap="round"/>
                <line x1="60" y1="125" x2="180" y2="125" stroke="#7A5C33" stroke-width="5" stroke-linecap="round"/>
                <line x1="75" y1="145" x2="165" y2="145" stroke="#7A5C33" stroke-width="5" stroke-linecap="round"/>',
            'card-classic' => '
                <rect width="240" height="240" fill="#F0EEF7"/>
                <rect x="45" y="35" width="150" height="170" fill="#FFFFFF" stroke="#C9C2E0" stroke-width="4"/>
                <line x1="70" y1="80" x2="170" y2="80" stroke="#B9B3D6" stroke-width="5" stroke-linecap="round"/>
                <line x1="70" y1="105" x2="170" y2="105" stroke="#B9B3D6" stroke-width="5" stroke-linecap="round"/>
                <line x1="70" y1="130" x2="140" y2="130" stroke="#B9B3D6" stroke-width="5" stroke-linecap="round"/>
                <line x1="70" y1="165" x2="130" y2="165" stroke="#8F87BC" stroke-width="5" stroke-linecap="round"/>',
            'card-floral' => '
                <rect width="240" height="240" fill="#FBF6F8"/>
                <rect x="45" y="35" width="150" height="170" fill="#FFFFFF" stroke="#E7A6C4" stroke-width="4"/>
                <path d="M45 35 C70 35 70 60 45 65 Z" fill="#E7A6C4"/>
                <path d="M195 205 C170 205 170 180 195 175 Z" fill="#E7A6C4"/>
                <line x1="70" y1="110" x2="170" y2="110" stroke="#C97CA0" stroke-width="5" stroke-linecap="round"/>
                <line x1="70" y1="135" x2="150" y2="135" stroke="#C97CA0" stroke-width="5" stroke-linecap="round"/>',
            'card-photo' => '
                <rect width="240" height="240" fill="#F3F1EE"/>
                <rect x="45" y="35" width="150" height="170" fill="#FFFFFF" stroke="#C9C2B4" stroke-width="4"/>
                <rect x="65" y="55" width="110" height="80" fill="#E4DFD3" stroke="#B7AF9E" stroke-width="3"/>
                <line x1="75" y1="160" x2="165" y2="160" stroke="#B7AF9E" stroke-width="5" stroke-linecap="round"/>
                <line x1="75" y1="180" x2="140" y2="180" stroke="#B7AF9E" stroke-width="5" stroke-linecap="round"/>',
            default => '<rect width="240" height="240" fill="#EEEBFB"/>',
        };

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240">'.$body.'</svg>';
    }
}
