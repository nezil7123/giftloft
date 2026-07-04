<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Show the cart (works for guests too — checkout is where login is required).
     */
    public function index()
    {
        return Inertia::render('Public/Cart', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Product $product)
    {
        abort_unless($product->is_active, 404);

        Cart::add($product->id);

        return back()->with('success', "“{$product->name}” added to cart.");
    }

    public function update(Request $request, Product $product)
    {
        Cart::update($product->id, (int) $request->input('quantity', 1));

        return back();
    }

    public function remove(Product $product)
    {
        Cart::remove($product->id);

        return back()->with('success', 'Removed from cart.');
    }
}
