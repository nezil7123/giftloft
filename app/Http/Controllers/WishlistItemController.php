<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistItemRequest;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WishlistItemController extends Controller
{
    /**
     * Add a catalog product to one of the user's wishlists.
     */
    public function addProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'wishlist_id' => ['required', 'integer', 'exists:wishlists,id'],
        ]);

        $wishlist = Wishlist::findOrFail($data['wishlist_id']);
        $this->authorize('update', $wishlist);

        abort_unless($product->is_active, 404);

        // Store-only registry: if this product is already on the list, just bump
        // the quantity rather than creating a duplicate row.
        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->increment('quantity');

            return back(303)->with('success', "Increased the quantity of “{$product->name}” in {$wishlist->name}.");
        }

        $wishlist->items()->create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'title' => $product->name,
            'product_url' => $product->product_url,
            'image_url' => $product->image_url,
            'price' => $product->price,
            'quantity' => 1,
            'note' => $product->description ? Str::limit($product->description, 200) : null,
            'is_giftable' => true,
            'status' => 'available',
        ]);

        return back(303)->with('success', "“{$product->name}” was added to {$wishlist->name}.");
    }

    /**
     * Update the owner-editable fields of a catalog item. Title, price, image
     * and link come from the catalog product and are not editable here.
     */
    public function update(WishlistItemRequest $request, Wishlist $wishlist, WishlistItem $item)
    {
        $this->authorize('update', $wishlist);
        abort_unless($item->wishlist_id === $wishlist->id, 404);

        $item->update($request->validated());

        return back()->with('success', 'Item updated.');
    }

    /**
     * Remove an item.
     */
    public function destroy(Wishlist $wishlist, WishlistItem $item)
    {
        $this->authorize('update', $wishlist);
        abort_unless($item->wishlist_id === $wishlist->id, 404);

        $item->delete();

        return back()->with('success', 'Item removed.');
    }
}
