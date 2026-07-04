<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // [name, category, gender, price, emoji, accent, description]
        $products = [
            // Wedding
            ['Hand-poured Soy Candle Set', 'wedding', 'unisex', 1299, '🕯️', 'amber', 'A trio of slow-burning candles in warm amber, fig, and sandalwood.'],
            ['Marble Cheese & Serving Board', 'wedding', 'unisex', 2499, '🧀', 'neutral', 'Italian marble board with brass handles — made for entertaining.'],
            ['Linen Bedding Bundle', 'wedding', 'unisex', 5999, '🛏️', 'rose', 'Stonewashed French linen in a calming oat tone. Queen size.'],
            ['Copper Cocktail Kit', 'wedding', 'unisex', 3299, '🍸', 'amber', 'Everything two hosts need for date-night cocktails at home.'],

            // Birthday
            ['Bluetooth Vinyl Speaker', 'birthday', 'unisex', 4499, '🔊', 'indigo', 'Retro-styled speaker with room-filling warm sound.'],
            ['Polaroid Instant Camera', 'birthday', 'unisex', 6999, '📸', 'violet', 'Snap and print memories instantly in vivid colour.'],
            ['Artisan Chocolate Box', 'birthday', 'unisex', 449, '🍫', 'amber', 'Twelve single-origin truffles, hand-finished.'],
            ['Enamel Pin Gift Pack', 'birthday', 'kids', 299, '📌', 'rose', 'A cheerful set of five collectible enamel pins.'],

            // Baby / kids
            ['Organic Cotton Baby Bundle', 'baby', 'kids', 1899, '👶', 'sky', 'Ultra-soft bodysuits, bib, and hat in a keepsake box.'],
            ['Wooden Stacking Toy', 'baby', 'kids', 799, '🧸', 'emerald', 'Heirloom-quality stacker in non-toxic finishes.'],
            ['Plush Story Bunny', 'baby', 'kids', 399, '🐰', 'rose', 'A snuggly bunny with an embroidered keepsake tag.'],
            ['Night Sky Projector', 'baby', 'kids', 2199, '🌙', 'indigo', 'Soothing starlight projector with gentle lullabies.'],
            ['Dino Print Backpack', 'fashion', 'kids', 999, '🎒', 'emerald', 'Lightweight backpack with a roaring dino print, kid-sized straps.'],
            ['Kids Light-up Sneakers', 'fashion', 'kids', 1499, '👟', 'sky', 'Sneakers that light up with every step — machine washable.'],

            // Home & living
            ['Ceramic Pour-over Set', 'home', 'unisex', 1599, '☕', 'neutral', 'Stoneware dripper and matching mug for the perfect cup.'],
            ['Trailing Pothos in Planter', 'home', 'unisex', 649, '🪴', 'emerald', 'Easy-care plant in a hand-glazed ceramic planter.'],
            ['Weighted Throw Blanket', 'home', 'unisex', 3499, '🧶', 'violet', 'Cloud-soft, cocooning warmth for slow evenings.'],
            ['Brass Bookend Pair', 'home', 'unisex', 449, '📚', 'amber', 'Sculptural solid-brass bookends that age beautifully.'],

            // Experiences
            ['Couples Spa Day', 'experiences', 'unisex', 7999, '💆', 'rose', 'A half-day of massages and access to the thermal suite.'],
            ['Fine Dining for Two', 'experiences', 'unisex', 5499, '🍽️', 'indigo', 'A chef’s tasting menu with wine pairing.'],
            ['Hot Air Balloon Ride', 'experiences', 'unisex', 9999, '🎈', 'sky', 'Sunrise flight with sparkling toast on landing.'],
            ['Pottery Workshop', 'experiences', 'unisex', 1299, '🏺', 'emerald', 'A hands-on class — leave with your own glazed bowl.'],

            // Fashion
            ['Merino Wool Wrap Dress', 'fashion', 'female', 4299, '👗', 'rose', 'Effortless wrap silhouette in soft merino — day to evening.'],
            ['Tailored Linen Shirt', 'fashion', 'male', 2799, '👔', 'sky', 'Breathable linen shirt with a modern tailored fit.'],
            ['Cashmere Blend Scarf', 'fashion', 'unisex', 1899, '🧣', 'violet', 'Featherlight cashmere blend in a versatile neutral tone.'],
            ['Leather Chelsea Boots', 'fashion', 'male', 5999, '🥾', 'neutral', 'Hand-finished leather boots that only get better with age.'],
            ['Silk Slip Skirt', 'fashion', 'female', 3299, '👘', 'rose', 'Bias-cut silk slip that drapes beautifully.'],

            // Jewellery
            ['Gold Vermeil Hoop Earrings', 'jewellery', 'female', 1999, '💍', 'amber', '18k gold vermeil hoops — lightweight and everyday-wearable.'],
            ['Engraved Signet Ring', 'jewellery', 'male', 2999, '💍', 'neutral', 'A solid brass signet ring, engravable with initials.'],
            ['Pearl Drop Necklace', 'jewellery', 'female', 3499, '📿', 'rose', 'Freshwater pearl pendant on a delicate gold chain.'],
            ['Minimalist Cufflinks', 'jewellery', 'male', 1599, '📎', 'indigo', 'Brushed steel cufflinks for a sharp, understated finish.'],
            ['Charm Bracelet Starter Set', 'jewellery', 'kids', 899, '📿', 'sky', 'A playful starter bracelet with three interchangeable charms.'],

            // Gadgets
            ['Noise-Cancelling Earbuds', 'gadgets', 'unisex', 6499, '🎧', 'indigo', 'All-day comfort with adaptive noise cancellation.'],
            ['Smart Fitness Band', 'gadgets', 'unisex', 3999, '⌚', 'emerald', 'Sleep, heart-rate, and activity tracking in a slim band.'],
            ['Instant Photo Printer', 'gadgets', 'unisex', 4999, '🖨️', 'violet', 'Pocket-sized printer that turns phone photos into prints.'],
            ['Kids Educational Tablet', 'gadgets', 'kids', 5499, '📱', 'sky', 'Durable, parent-controlled tablet loaded with learning apps.'],
            ['Portable Espresso Maker', 'gadgets', 'unisex', 2999, '☕', 'amber', 'Manual espresso, no batteries needed — perfect for travel.'],
        ];

        foreach ($products as $i => [$name, $category, $gender, $price, $emoji, $accent, $description]) {
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => $category,
                    'gender' => $gender,
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
