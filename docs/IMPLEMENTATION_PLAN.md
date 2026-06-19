# Gift Loft Implementation Plan

This document captures the current implementation, completed features, and the work needed for future development.

## Current implementation

### Authentication and user flow
- Laravel authentication scaffolded with Breeze and email verification.
- `AuthenticatedLayout.vue` provides authenticated navigation and logout/profile actions.
- Admin middleware protects `/admin` routes.

### Dashboard and pages
- `DashboardController@index` renders `resources/js/Pages/Dashboard.vue`.
- `Admin\DashboardController@index` renders `resources/js/Pages/Admin/Dashboard.vue`.
- User dashboard shows counts for:
  - events
  - wishlists
  - gifts
  - messages
  - puzzles
- Admin dashboard shows site totals for:
  - users
  - events
  - gifts
  - puzzles

### Resource routes and controllers
- `EventController@index` renders `resources/js/Pages/Events/Index.vue`.
- `WishlistController@index` renders `resources/js/Pages/Wishlists/Index.vue`.
- `GiftController@index` renders `resources/js/Pages/Gifts/Index.vue`.
- Authenticated routes are defined in `routes/web.php`.

### Frontend build
- Tailwind CSS, Vite, and Inertia are configured and compile successfully.
- `npm run build` produces valid asset output.

## Key files

- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/EventController.php`
- `app/Http/Controllers/WishlistController.php`
- `app/Http/Controllers/GiftController.php`
- `routes/web.php`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/Pages/Admin/Dashboard.vue`
- `resources/js/Pages/Events/Index.vue`
- `resources/js/Pages/Wishlists/Index.vue`
- `resources/js/Pages/Gifts/Index.vue`

## Remaining implementation tasks

### High-priority features
1. Complete event CRUD and detail pages
2. Add wishlist creation/editing and wishlist item flows
3. Implement Razorpay payment checkout and webhook processing
4. Add gift purchase and tracking views
5. Implement messaging and encrypted chat flows
6. Add puzzle creation and solve experience

### Admin and moderation
- Add admin resource pages for users, events, gifts, and reports
- Add analytics widgets and activity review screens
- Add user moderation and content approval workflows

### Social and discovery
- Add user profile pages and public profile sharing
- Add search for users, events, and wishlists
- Add follows and likes for social content

### Infrastructure and polish
- Add model validation, authorization policies, and request classes
- Add tests for controller routes and page loads
- Add notifications and email templates
- Add responsive UI polish and animations

## Recommended next steps

1. Build the event creation page and event details route.
2. Implement wishlist item management and public wishlist URLs.
3. Add payment integration with Razorpay and payment status tracking.
4. Flesh out admin dashboard pages for user and event management.
5. Add tests for the new CRUD flows and route protections.

## WithJoy-inspired feature set

Gift Loft should align with the rich event and gifting experience seen on WithJoy while keeping our product uniquely focused on modern gifting and registry flows.

### Core inspiration from WithJoy
- A public event website builder with elegant landing pages for weddings and celebrations.
- An all-in-one gift list/registry experience where guests can browse and purchase gifts directly.
- Guest planning tools such as RSVP and guest list management.
- Invitations, save-the-dates, and other event communications.
- Mobile-friendly, polished product pages with minimal, light design.
- Content sections that showcase product benefits, customer trust, and workflow simplicity.

### Features to adopt for Gift Loft
- `Landing page` with hero copy, simple feature cards, and a strong primary CTA.
- `Public shop` for browsing available gifts and fund items without login.
- `Gift detail` pages with direct purchase flow and elegant product presentation.
- `Event page` builder for publishing celebration pages and registries.
- `Wishlist / registry` flows that support both gift items and cash/experience funds.
- `Guest RSVP` and guest list management as part of the event toolkit.
- `Responsive design` and a polished light visual theme for all public-facing pages.

## Updated requirements

### Public experience
- Replace the default Laravel welcome page with a branded Gift Loft landing page.
- Create a `Shop` experience for public gift browsing at `/shop`.
- Add gift detail pages at `/shop/{gift}` with direct purchase CTAs.
- Provide a clear public pathway to register and create an event/registry.
- Include feature blocks that highlight event pages, registries, direct shopping, and guest planning.

### Event and registry flows
- Support event creation with event-specific landing pages and gift lists.
- Allow owners to create public wishlists with item details and purchase options.
- Enable public access to gift lists while preserving admin-only edit controls.
- Add the ability to track gifts, purchases, and event progress in the user dashboard.

### Guest and communication tools
- Offer RSVP-style guest list management tied to events.
- Plan for invitations and save-the-date-style content in future iterations.
- Build customer-facing communication features with high usability and minimal steps.

### Commerce and payments
- Prioritize Razorpay checkout integration for direct purchase flows.
- Support purchasing from public shop pages and registry items.
- Track payment status and gift fulfillment in the dashboard.

### Design and UX
- Emphasize a minimalist, light color palette with soft shadows and rounded cards.
- Use simple typography, clear spacing, and modern CTA buttons.
- Add customer testimonial/feature highlight sections on the landing page.
- Keep the UI consistent across public and authenticated areas.

## Notes for future implementers

- Keep the current route names and controller structure when adding new features.
- Use Inertia page components for new screens and keep layout consistency in `AuthenticatedLayout.vue`.
- Store common UI cards and buttons in reusable Vue components if the design expands.
- Prioritize secure authorization checks for admin and user-owned resources.
