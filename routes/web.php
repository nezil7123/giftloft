<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GiftAddonController as AdminGiftAddonController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventDesignController;
use App\Http\Controllers\EventPhotoController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RazorpayWebhookController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\ProfileController as PublicProfileController;
use App\Http\Controllers\Public\ShopController;
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

// Signature-authenticated (see RazorpayWebhookController); CSRF-exempt.
Route::post('/webhooks/razorpay', RazorpayWebhookController::class)->name('webhooks.razorpay');

Route::get('/privacy', fn () => Inertia::render('Public/Legal/Privacy'))->name('legal.privacy');
Route::get('/terms', fn () => Inertia::render('Public/Legal/Terms'))->name('legal.terms');

Route::get('/shop', [ShopController::class, 'index'])->name('public.shop');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('public.shop.show');

// Cart works for guests too — checkout is where login is required.
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/templates', [PublicTemplateController::class, 'index'])->name('public.templates');
Route::get('/templates/website/{key}', [PublicTemplateController::class, 'website'])->name('public.templates.website');

Route::get('/e/{shareCode}', [PublicEventController::class, 'show'])->name('public.event');
Route::get('/e/{shareCode}/invitation', [PublicEventController::class, 'invitation'])->name('public.event.invitation');
Route::get('/r/{slug}', [PublicWishlistController::class, 'show'])->name('public.wishlist');
Route::get('/u/{identifier}', [PublicProfileController::class, 'show'])->name('public.profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/{guide}', [HelpController::class, 'show'])->name('help.show');

    // Exact-match people search; throttled to block enumeration attempts.
    Route::get('/people', [UserSearchController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('people.find');

    Route::resource('events', EventController::class);
    Route::get('events/{event}/design', [EventDesignController::class, 'edit'])->name('events.design.edit');
    Route::put('events/{event}/design', [EventDesignController::class, 'update'])->name('events.design.update');
    Route::post('events/{event}/photos', [EventPhotoController::class, 'store'])->name('events.photos.store');
    Route::delete('events/{event}/photos', [EventPhotoController::class, 'destroy'])->name('events.photos.destroy');
    Route::put('events/{event}/photos/order', [EventPhotoController::class, 'reorder'])->name('events.photos.reorder');

    Route::resource('wishlists', WishlistController::class);
    Route::post('registry/products/{product}', [WishlistItemController::class, 'addProduct'])->name('registry.products.add');
    Route::put('wishlists/{wishlist}/items/{item}', [WishlistItemController::class, 'update'])->name('wishlists.items.update');
    Route::delete('wishlists/{wishlist}/items/{item}', [WishlistItemController::class, 'destroy'])->name('wishlists.items.destroy');

    // Gift checkout (gifting a wishlist item)
    Route::get('/checkout/item/{item}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/item/{item}', [CheckoutController::class, 'store'])->name('checkout.store');

    // Cart checkout — buy for yourself or send everything as a personal gift.
    Route::get('/checkout/cart', [CheckoutController::class, 'cart'])->name('checkout.cart');
    Route::post('/checkout/cart', [CheckoutController::class, 'storeCart'])->name('checkout.cart.store');
    Route::post('/checkout/cart/order', [CheckoutController::class, 'createCartOrder'])->name('checkout.cart.order');

    Route::resource('gifts', GiftController::class)->only(['index', 'show']);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->middleware([AdminMiddleware::class])->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/moderation', [AdminEventController::class, 'index'])->name('moderation.index');
        Route::put('/events/{event}/takedown', [AdminEventController::class, 'takedown'])->name('events.takedown');
        Route::put('/events/{event}/restore', [AdminEventController::class, 'restore'])->name('events.restore');
        Route::put('/wishlists/{wishlist}/toggle', [AdminEventController::class, 'toggleWishlist'])->name('wishlists.toggle');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

        Route::get('/gift-addons', [AdminGiftAddonController::class, 'index'])->name('gift-addons.index');
        Route::post('/gift-addons', [AdminGiftAddonController::class, 'store'])->name('gift-addons.store');
        Route::put('/gift-addons/{giftAddon}', [AdminGiftAddonController::class, 'update'])->name('gift-addons.update');
        Route::delete('/gift-addons/{giftAddon}', [AdminGiftAddonController::class, 'destroy'])->name('gift-addons.destroy');
    });
});

require __DIR__.'/auth.php';
