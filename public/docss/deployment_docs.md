# Deployment Guide — FTTX Network Manager

## 1. Server Requirements

| Requirement | Version |
|---|---|
| Laravel | 13.x (`^13.17`) |
| PHP | 8.3 or higher |
| MySQL | 8.0+ (or MariaDB 10.3+) |
| Composer | 2.x |
| Node.js | 20+ (build time only, for Vite assets) |
| Web server | Nginx or Apache with PHP-FPM |

### Required PHP extensions
`pdo`, `pdo_mysql` (and/or `pdo_pgsql` if using Postgres), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

### File permissions
`storage/` and `bootstrap/cache/` must be writable by the web server user (`www-data` on most distros).

### Recommended
- SSL/HTTPS enabled on the production domain
- `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` set to `database` (not `file`) if the hosting filesystem is ephemeral

---

## 2. Environment Configuration

Copy `.env.example` to `.env` and set the following before deploying:

```env
APP_NAME="FTTX Network Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

APP_KEY=                # generate once, see below — do not leave blank

DB_CONNECTION=mysql      # or pgsql
DB_HOST=127.0.0.1
DB_PORT=3306             # 5432 for pgsql
DB_DATABASE=ftt_management
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Generate a production `APP_KEY` once and keep it fixed across deploys — regenerating it on every deploy invalidates all existing sessions and encrypted data:

```bash
php artisan key:generate --show
```
Copy the `base64:...` output into `APP_KEY` in `.env` (or your host's environment variables panel).

---

## 3. Standard Deployment Steps (any Linux server with PHP/Nginx/MySQL already installed)

```bash
# 1. Get the code onto the server
git clone https://github.com/md-borno/FTT_Management.git
cd FTT_Management

# 2. Install PHP dependencies (production, no dev packages)
composer install --no-dev --optimize-autoloader

# 3. Install and build frontend assets
npm install
npm run build

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Run database migrations
php artisan migrate --force

# 6. Cache config, routes, and views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Fix permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 8. Point your web server's document root at /public
```

### Nginx example site config
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/FTT_Management/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Deploying with Docker (recommended for platforms without native PHP support, e.g. Render)

Render, and several other modern PaaS platforms, don't support PHP as a native runtime — only Node.js, Python, Ruby, Go, Rust, and Elixir. Docker is the standard way to run PHP on these platforms.

A production-ready `Dockerfile` (multi-stage: Node build → PHP 8.3-fpm + nginx via supervisord) along with `docker/nginx.conf`, `docker/php-fpm.conf`, `docker/supervisord.conf`, and `docker/entrypoint.sh` should sit at the project root. The entrypoint script:
- Binds nginx to the platform's dynamic `$PORT`
- Optionally runs `php artisan migrate --force` on boot (`RUN_MIGRATIONS=true`)
- Runs `config:cache`, `route:cache`, `view:cache`
- Fixes storage/cache permissions on every boot

### On Render specifically
1. Create the web service with **Runtime: Docker** (must be chosen at service creation — cannot be switched on an existing Node/PHP-buildpack service after the fact)
2. Attach a managed database (Render Postgres, or an external MySQL instance)
3. Set environment variables in the Render dashboard: `APP_KEY`, `DB_*`, `RUN_MIGRATIONS=true` (first deploy only), etc.
4. Render provides `PORT` automatically — no action needed, the entrypoint reads it

---

## 5. Post-Deployment Checklist

- [ ] Confirm the site loads over HTTPS with no mixed-content warnings
- [ ] Confirm `APP_DEBUG=false` (never `true` in production — leaks stack traces)
- [ ] Confirm migrations ran cleanly: `php artisan migrate:status`
- [ ] Confirm queued jobs process, if any are used: `php artisan queue:work` (or a supervisor process for it)
- [ ] Confirm file uploads persist correctly — if the host's filesystem is ephemeral (common on PaaS platforms), switch `FILESYSTEM_DISK` to `s3` rather than relying on local `storage/app`
- [ ] Set up a scheduled task runner for `php artisan schedule:run` (cron every minute) if the app uses Laravel's task scheduler
- [ ] Rotate any database credentials that were ever committed to the repo in plaintext

---

## 6. Known Issues to Resolve Before Production

- **PHP version drift between environments** — confirm whether `composer.json` in the deployed branch requires `^8.3` or `^8.4` (this has appeared as both at different points); make sure the production PHP version matches exactly.
- **Local dev PHP mismatch** — if developing via XAMPP, confirm XAMPP's bundled PHP version matches the CLI/system PHP version used to run `artisan` commands; a mismatch here can mask browser-facing errors that don't show up in terminal testing.
- **Database engine consistency** — decide on MySQL/MariaDB vs. PostgreSQL for production and keep local development on the same engine to avoid environment-specific bugs.
- **`MaintenanceSchedule` / `PerformanceMetric` models** currently have no matching database migration — any code path touching them will error until migrations are added.
- **Duplicate auth routing** — `routes/web.php` defines login/logout/profile-update as closures alongside Breeze's dedicated `Auth/*` controllers; consolidate before production to avoid inconsistent behavior.