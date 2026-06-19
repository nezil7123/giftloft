# Gift Loft - Project Setup Instructions

Gift Loft is a modern event invitation and gifting platform built with Laravel, Vue 3, Inertia.js, and Tailwind CSS.

## Setup Checklist

- [x] Create copilot-instructions.md file
- [x] Initialize Laravel project structure
- [x] Create database models and migrations
- [x] Set up authentication and middleware
- [x] Configure Vue 3 + Inertia + Tailwind
- [x] Create API routes and controllers (partial)
- [ ] Set up Razorpay integration
- [ ] Create admin panel structure (partial)
- [ ] Create user dashboard components (partial)
- [x] Install dependencies and compile
- [x] Update README documentation

## Project Overview

**Gift Loft** is a comprehensive event and gifting platform with:
- Event management (Marriage, Birthday, Baptism, Engagement, etc.)
- Personal and event-based wishlists
- Admin panel with analytics
- User dashboard
- Social features with likes
- Encrypted messaging
- Puzzle challenge system
- Razorpay payment integration
- Beautiful, responsive UI with animations

## Current status

Implemented so far:
- Authenticated dashboard and admin middleware
- User dashboard metrics and action cards
- Event/wishlist/gift list pages with protected routes
- Admin dashboard totals
- Frontend build verified

Pending work:
- Event creation and full CRUD
- Wishlist item management and public sharing
- Gift purchase and Razorpay checkout
- Messaging and puzzle flows
- Admin management pages
- Social search and profile discovery

## Technology Stack

- **Backend**: Laravel 13+ (current scaffold)
- **Frontend**: Vue 3 + Inertia.js + Tailwind CSS
- **Database**: SQLite for local dev, can support MySQL/PostgreSQL
- **Payment**: Razorpay
- **Authentication**: Laravel Breeze + Socialite/Google OAuth

## Notes

- `routes/web.php` contains the registered authenticated and admin routes.
- `resources/js/Pages/Events/Index.vue`, `Wishlists/Index.vue`, and `Gifts/Index.vue` contain the current list screens.
- `docs/IMPLEMENTATION_PLAN.md` provides an implementation roadmap for future work.
