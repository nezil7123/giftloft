<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Wedding
            ['Hand-poured Soy Candle Set', 'wedding', 1299, '🕯️', 'amber', 'A trio of slow-burning candles in warm amber, fig, and sandalwood.'],
            ['Marble Cheese & Serving Board', 'wedding', 2499, '🧀', 'neutral', 'Italian marble board with brass handles — made for entertaining.'],
            ['Linen Bedding Bundle', 'wedding', 5999, '🛏️', 'rose', 'Stonewashed French linen in a calming oat tone. Queen size.'],
            ['Copper Cocktail Kit', 'wedding', 3299, '🍸', 'amber', 'Everything two hosts need for date-night cocktails at home.'],

            // Birthday
            ['Bluetooth Vinyl Speaker', 'birthday', 4499, '🔊', 'indigo', 'Retro-styled speaker with room-filling warm sound.'],
            ['Polaroid Instant Camera', 'birthday', 6999, '📸', 'violet', 'Snap and print memories instantly in vivid colour.'],
            ['Artisan Chocolate Box', 'birthday', 449, '🍫', 'amber', 'Twelve single-origin truffles, hand-finished.'],
            ['Enamel Pin Gift Pack', 'birthday', 299, '📌', 'rose', 'A cheerful set of five collectible enamel pins.'],

            // Baby
            ['Organic Cotton Baby Bundle', 'baby', 1899, '👶', 'sky', 'Ultra-soft bodysuits, bib, and hat in a keepsake box.'],
            ['Wooden Stacking Toy', 'baby', 799, '🧸', 'emerald', 'Heirloom-quality stacker in non-toxic finishes.'],
            ['Plush Story Bunny', 'baby', 399, '🐰', 'rose', 'A snuggly bunny with an embroidered keepsake tag.'],
            ['Night Sky Projector', 'baby', 2199, '🌙', 'indigo', 'Soothing starlight projector with gentle lullabies.'],

            // Home & living
            ['Ceramic Pour-over Set', 'home', 1599, '☕', 'neutral', 'Stoneware dripper and matching mug for the perfect cup.'],
            ['Trailing Pothos in Planter', 'home', 649, '🪴', 'emerald', 'Easy-care plant in a hand-glazed ceramic planter.'],
            ['Weighted Throw Blanket', 'home', 3499, '🧶', 'violet', 'Cloud-soft, cocooning warmth for slow evenings.'],
            ['Brass Bookend Pair', 'home', 449, '📚', 'amber', 'Sculptural solid-brass bookends that age beautifully.'],

            // Experiences
            ['Couples Spa Day', 'experiences', 7999, '💆', 'rose', 'A half-day of massages and access to the thermal suite.'],
            ['Fine Dining for Two', 'experiences', 5499, '🍽️', 'indigo', 'A chef’s tasting menu with wine pairing.'],
            ['Hot Air Balloon Ride', 'experiences', 9999, '🎈', 'sky', 'Sunrise flight with sparkling toast on landing.'],
            ['Pottery Workshop', 'experiences', 1299, '🏺', 'emerald', 'A hands-on class — leave with your own glazed bowl.'],
        ];

        foreach ($products as $i => [$name, $category, $price, $emoji, $accent, $description]) {
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'emoji' => $emoji,
                    'accent' => $accent,
                    'description' => $description,
                    'is_active' => true,
                    'sort_order' => $i,
                ],
            );
        }
    }
}
