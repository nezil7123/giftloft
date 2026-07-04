<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Event;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * List the user's wishlists.
     */
    public function index(Request $request)
    {
        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with('event:id,title')
            ->withCount('items')
            ->latest()
            ->get();

        return Inertia::render('Wishlists/Index', [
            'wishlists' => $wishlists,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Wishlists/Create', [
            'events' => $this->userEvents($request),
        ]);
    }

    public function store(WishlistRequest $request)
    {
        $wishlist = Wishlist::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'slug' => $this->uniqueSlug($request->input('name')),
        ]);

        return redirect()
            ->route('wishlists.show', $wishlist)
            ->with('success', 'Wishlist created.');
    }

    /**
     * Show the wishlist with its items (wishlist builder).
     */
    public function show(Wishlist $wishlist)
    {
        $this->authorize('view', $wishlist);

        $wishlist->load(['event:id,title', 'items' => fn ($q) => $q->latest()]);

        return Inertia::render('Wishlists/Show', [
            'wishlist' => $wishlist,
        ]);
    }

    public function edit(Request $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);

        return Inertia::render('Wishlists/Edit', [
            'wishlist' => $wishlist,
            'events' => $this->userEvents($request),
        ]);
    }

    public function update(WishlistRequest $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);

        $wishlist->update($request->validated());

        return redirect()
            ->route('wishlists.show', $wishlist)
            ->with('success', 'Wishlist updated.');
    }

    public function destroy(Wishlist $wishlist)
    {
        $this->authorize('delete', $wishlist);

        $wishlist->delete();

        return redirect()
            ->route('wishlists.index')
            ->with('success', 'Wishlist deleted.');
    }

    /**
     * The current user's events, for the wishlist <-> event picker.
     */
    protected function userEvents(Request $request)
    {
        return Event::where('user_id', $request->user()->id)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    /**
     * Generate a unique slug from the wishlist name.
     */
    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'wishlist';
        $slug = $base;
        $i = 2;
        while (Wishlist::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
