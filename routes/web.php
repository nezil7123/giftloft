<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventDesignController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\GiftController as PublicGiftController;
use App\Http\Controllers\Public\TemplateController as PublicTemplateController;
use App\Http\Controllers\Public\WishlistController as PublicWishlistController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\WishlistItemController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Public/Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/shop', [PublicGiftController::class, 'index'])->name('public.shop');
Route::get('/shop/{gift}', [PublicGiftController::class, 'show'])->name('public.shop.show');

Route::get('/templates', [PublicTemplateController::class, 'index'])->name('public.templates');
Route::get('/templates/website/{key}', [PublicTemplateController::class, 'website'])->name('public.templates.website');

Route::get('/e/{shareCode}', [PublicEventController::class, 'show'])->name('public.event');
Route::get('/e/{shareCode}/invitation', [PublicEventController::class, 'invitation'])->name('public.event.invitation');
Route::get('/r/{slug}', [PublicWishlistController::class, 'show'])->name('public.wishlist');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('events', EventController::class);
    Route::get('events/{event}/design', [EventDesignController::class, 'edit'])->name('events.design.edit');
    Route::put('events/{event}/design', [EventDesignController::class, 'update'])->name('events.design.update');

    Route::resource('wishlists', WishlistController::class);
    Route::post('wishlists/{wishlist}/items', [WishlistItemController::class, 'store'])->name('wishlists.items.store');
    Route::put('wishlists/{wishlist}/items/{item}', [WishlistItemController::class, 'update'])->name('wishlists.items.update');
    Route::delete('wishlists/{wishlist}/items/{item}', [WishlistItemController::class, 'destroy'])->name('wishlists.items.destroy');

    // Gift checkout (gifting a wishlist item)
    Route::get('/checkout/item/{item}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/item/{item}', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::resource('gifts', GiftController::class)->only(['index', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->middleware([AdminMiddleware::class])->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
