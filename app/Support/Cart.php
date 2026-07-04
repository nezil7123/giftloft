<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * A lightweight, session-based shopping cart. Works for guests and logged-in
 * users alike since it never touches the database — checkout is where a
 * real Order gets created.
 */
class Cart
{
    protected const SESSION_KEY = 'cart';

    protected const MAX_QTY = 20;

    /**
     * @return array<int, int> product_id => quantity
     */
    public static function raw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $cart = self::raw();
        $cart[$productId] = min(self::MAX_QTY, ($cart[$productId] ?? 0) + $qty);
        session([self::SESSION_KEY => $cart]);
    }

    public static function update(int $productId, int $qty): void
    {
        $cart = self::raw();

        if ($qty < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min(self::MAX_QTY, $qty);
        }

        session([self::SESSION_KEY => $cart]);
    }

    public static function remove(int $productId): void
    {
        $cart = self::raw();
        unset($cart[$productId]);
        session([self::SESSION_KEY => $cart]);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function count(): int
    {
        return array_sum(self::raw());
    }

    /**
     * Resolve cart contents against live, active products — stale or
     * deactivated products are silently dropped from the cart.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: float}>
     */
    public static function items(): Collection
    {
        $cart = self::raw();

        if (! $cart) {
            return collect();
        }

        return Product::active()->whereIn('id', array_keys($cart))->get()
            ->map(fn (Product $product) => [
                'product' => $product,
                'quantity' => $cart[$product->id],
                'line_total' => round((float) $product->price * $cart[$product->id], 2),
            ])
            ->values();
    }

    public static function subtotal(): float
    {
        return round((float) self::items()->sum('line_total'), 2);
    }
}
