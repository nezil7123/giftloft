# ComeYay — production deployment files

Two artifacts, both generated fresh (no dev/test data):

| File | What it is |
|---|---|
| `comeyay-production.sql` | Full schema + admin user + 65 products + 10 gift addons |
| `comeyay-images.tar.gz` | The 38 image files the imported rows point at — 28 product photos and 10 gift-addon graphics |

**Event website and invitation templates are code, not data** — they ship with
the repo and need nothing imported.

## 1. Import the database

```bash
mysql -u YOUR_USER -p -e "CREATE DATABASE comeyay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u YOUR_USER -p comeyay < comeyay-production.sql
```

Verified to import cleanly on MySQL 5.7+, MySQL 8.x/9.x and MariaDB 10.x.
The `migrations` table is pre-filled (30/30), so `php artisan migrate` reports
nothing pending and future migrations still apply normally.

## 2. Restore the images

```bash
tar -xzf comeyay-images.tar.gz -C storage/app/public
php artisan storage:link
```

This unpacks two directories:

- `products/` — 28 photos. Skip it and 28 of the 65 products show blank tiles.
  Their URLs are already rewritten to `https://comeyay.com/storage/products/...`.
- `gift-addons/` — 10 SVGs for the packaging, sticker and card pickers at
  checkout. Skip it and every gift-wrapping option renders without artwork.

`php artisan storage:link` is what makes `/storage/...` resolve at all, so a
missing symlink breaks both sets at once.

## 3. Configure `.env`

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://comeyay.com

DB_CONNECTION=mysql
DB_DATABASE=comeyay
DB_USERNAME=...
DB_PASSWORD=...
```

`APP_URL` matters beyond cosmetics: canonical tags, `sitemap.xml`, `robots.txt`
and the Open Graph image are all derived from it.

## 4. Cache and go

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Already imported an older copy of this dump?

Two things have changed since the first build. Both are safe to run on a
database that already has data in it.

**1. RSVPs (new).** Guests can now reply to an event with a meal preference and
whether they need a room. That needs a new table:

```sql
CREATE TABLE `rsvps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'attending',
  `party_size` smallint unsigned NOT NULL DEFAULT '1',
  `meal_preference` varchar(255) DEFAULT NULL,
  `needs_accommodation` tinyint(1) NOT NULL DEFAULT '0',
  `note` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rsvps_event_id_email_unique` (`event_id`,`email`),
  KEY `rsvps_user_id_foreign` (`user_id`),
  KEY `rsvps_event_id_status_index` (`event_id`,`status`),
  CONSTRAINT `rsvps_event_id_foreign` FOREIGN KEY (`event_id`)
    REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rsvps_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
VALUES ('2026_09_21_120000_create_rsvps_table',
        (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0)+1 FROM `migrations`) b));
```

The `migrations` row matters: without it `php artisan migrate` will try to
create the table a second time and fail.

**2. Gift-addon images were missing from the first images archive.** If your
checkout shows gift-wrapping options with no artwork, that is why — re-extract
from the new `comeyay-images.tar.gz` (see step 2 above). No database change is
involved; the rows always pointed at `/storage/gift-addons/...`.

**3. `events.status` default.** It changed from `draft` to `published`, so new
events go live as soon as they're created. If you imported before that change:

```sql
ALTER TABLE events MODIFY status VARCHAR(255) NOT NULL DEFAULT 'published';
```

Existing events keep whatever status they already have.

## Admin account

Sign in at `/login`, then **change the password immediately** under
Settings → Password. The generated password was shown once in the terminal
when these files were built; it is deliberately not stored in this repo.

If you'd rather reset it on the server:

```bash
php artisan tinker --execute="\$u=App\Models\User::where('email','admin@comeyay.com')->first(); \$u->forceFill(['password'=>Hash::make('YOUR-NEW-PASSWORD')])->save(); echo 'done';"
```
