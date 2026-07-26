#!/bin/sh
set -e

echo "=== Lincoln Hostel — Starting Deployment ==="
echo "Clearing all caches..."
php artisan optimize:clear

echo "Warming production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Creating storage symlink if missing..."
php artisan storage:link --force || true

echo "Running migrations..."
php artisan migrate --force || true

echo "=== Cache warm complete, starting services ==="

# Start supervisor (nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
