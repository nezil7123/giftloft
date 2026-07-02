<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class ShopController extends Controller
{
    /**
     * Browse the curated gift catalog.
     */
    public function index()
    {
        return Inertia::render('Public/Shop', [
            'products' => Product::active()->orderBy('sort_order')->get(),
            'categories' => Product::categories(),
            'myWishlists' => $this->myWishlists(),
            'filters' => [
                'category' => request('category'),
                'max_price' => request('max_price') !== null ? (int) request('max_price') : null,
            ],
        ]);
    }

    /**
     * View a single catalog product.
     */
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return Inertia::render('Public/ProductDetail', [
            'product' => $product,
            'related' => Product::active()
                ->where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->orderBy('sort_order')
                ->take(3)
                ->get(),
            'categories' => Product::categories(),
            'myWishlists' => $this->myWishlists(),
        ]);
    }

    /**
     * The current user's registries (for the "Add to registry" picker), or null when guest.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>|null
     */
    protected function myWishlists()
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return $user->wishlists()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($w) => ['id' => $w->id, 'name' => $w->name]);
    }
}
