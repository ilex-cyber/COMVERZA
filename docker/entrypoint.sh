#!/bin/sh
set -e
cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache public/uploads

[ -f .env ] || cp .env.example .env
[ -f vendor/autoload.php ] || composer install --no-interaction --prefer-dist --no-progress

grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

php artisan migrate --force
# Siembra datos de demostración solo la primera vez
php artisan db:seed --force --class=DatabaseSeeder --no-interaction || true

chown -R www-data:www-data storage bootstrap/cache public/uploads 2>/dev/null || true

exec apache2-foreground
