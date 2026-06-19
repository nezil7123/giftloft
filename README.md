# Gift Loft

Gift Loft is a modern event invitation and gifting platform built with Laravel, Vue 3, Inertia.js, and Tailwind CSS.

## Current implementation

### Completed scaffolding
- Laravel application initialized with authentication and email verification
- Vue 3 + Inertia.js frontend integrated with Tailwind CSS
- Basic authenticated layouts and dashboard pages implemented
- Admin guard and middleware set up for `/admin` route protection
- Resource routes and index controllers created for:
  - Events
  - Wishlists
  - Gifts
- User dashboard shows metrics for events, wishlists, gifts, messages, and puzzles
- Admin dashboard shows site totals for users, events, gifts, and puzzles
- Frontend build works with `npm run build`

### Key files and structure
- `routes/web.php` — application and protected routes
- `app/Http/Controllers/DashboardController.php` — user dashboard data
- `app/Http/Controllers/Admin/DashboardController.php` — admin dashboard data
- `app/Http/Controllers/EventController.php` — event list page
- `app/Http/Controllers/WishlistController.php` — wishlist list page
- `app/Http/Controllers/GiftController.php` — gift list page
- `resources/js/Layouts/AuthenticatedLayout.vue` — authenticated navigation and layout
- `resources/js/Pages/Dashboard.vue` — primary user dashboard view
- `resources/js/Pages/Admin/Dashboard.vue` — admin overview page
- `resources/js/Pages/Events/Index.vue`
- `resources/js/Pages/Wishlists/Index.vue`
- `resources/js/Pages/Gifts/Index.vue`

## Pending implementation

### Core features still to build
- Full event CRUD, event detail pages, invitation and guest flows
- Wishlist CRUD, wishlist items, public sharing, and registry behavior
- Gift purchase flow with Razorpay integration
- Messaging and encrypted chat between users
- Puzzle challenge creation and solving
- Social interactions: likes, follows, user profiles, search pages
- Admin moderation panels, analytics dashboards, and reporting
- Payment and purchase history views
- Enhanced profile settings, privacy controls, and notifications

### Next development steps
1. Finalize domain models and fill controller CRUD methods
2. Build event creation and management pages
3. Add wishlist item management and public wishlist pages
4. Integrate Razorpay payment checkout and webhook handling
5. Add message and puzzle models/controllers/views
6. Add admin management pages for users, events, gifts, and reports
7. Add search, user discovery, and social activity feeds
8. Add tests for routes, controllers, and frontend pages

## Getting started locally

1. Copy `.env.example` to `.env`
2. Configure database connection
3. Install PHP dependencies: `composer install`
4. Install JS dependencies: `npm install`
5. Run migrations: `php artisan migrate`
6. Build assets: `npm run build`
7. Start local server: `php artisan serve`

## Documentation

For implementation details, feature status, and future work, see `docs/IMPLEMENTATION_PLAN.md`.

## License

This project is licensed under the MIT license.
