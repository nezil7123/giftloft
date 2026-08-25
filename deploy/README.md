# ComeYay — production deployment files

Two artifacts, both generated fresh (no dev/test data):

| File | What it is |
|---|---|
| `comeyay-production.sql` | Full schema + admin user + 65 products + 10 gift addons |
| `comeyay-product-images.tar.gz` | The 28 product image files the imported products point at |

**Event website and invitation templates are code, not data** — they ship with
the repo and need nothing imported.

## 1. Import the database

```bash
mysql -u YOUR_USER -p -e "CREATE DATABASE comeyay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u YOUR_USER -p comeyay < comeyay-production.sql
```

Verified to import cleanly on MySQL 5.7+, MySQL 8.x/9.x and MariaDB 10.x.
The `migrations` table is pre-filled (29/29), so `php artisan migrate` reports
nothing pending and future migrations still apply normally.

## 2. Restore the product images

```bash
tar -xzf comeyay-product-images.tar.gz -C storage/app/public
php artisan storage:link
```

Skip this and 28 of the 65 products show broken images. Their URLs are already
rewritten to `https://comeyay.com/storage/products/...`.

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

## Admin account

Sign in at `/login`, then **change the password immediately** under
Settings → Password. The generated password was shown once in the terminal
when these files were built; it is deliberately not stored in this repo.

If you'd rather reset it on the server:

```bash
php artisan tinker --execute="\$u=App\Models\User::where('email','admin@comeyay.com')->first(); \$u->forceFill(['password'=>Hash::make('YOUR-NEW-PASSWORD')])->save(); echo 'done';"
```
