<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistItemRequest;
use App\Models\Wishlist;
use App\Models\WishlistItem;

class WishlistItemController extends Controller
{
    /**
     * Add an item to the wishlist.
     */
    public function store(WishlistItemRequest $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);

        $wishlist->items()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => 'available',
        ]);

        return back()->with('success', 'Item added to your registry.');
    }

    /**
     * Update an existing item.
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
