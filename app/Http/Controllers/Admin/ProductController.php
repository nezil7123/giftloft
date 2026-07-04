<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * List the full catalog (including inactive products).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('category', 'like', "%{$q}%")))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Products', [
            'products' => $products,
            'categories' => Product::categories(),
            'genders' => Product::genders(),
            'filters' => ['q' => $q],
        ]);
    }

    /**
     * Add a product to the catalog.
     */
    public function store(ProductRequest $request)
    {
        Product::create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug($request->validated()['name']),
            'sort_order' => $request->validated()['sort_order'] ?? (Product::max('sort_order') + 1),
        ]);

        return back()->with('success', 'Product added to the catalog.');
    }

    /**
     * Update a product (slug stays stable so wishlist links keep working).
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return back()->with('success', 'Product updated.');
    }

    /**
     * Remove a product from the catalog.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product removed.');
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 2;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
