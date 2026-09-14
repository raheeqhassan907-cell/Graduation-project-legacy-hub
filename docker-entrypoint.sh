#!/bin/sh
set -e

# 1. Ensure SQLite database file exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database file..."
    touch /var/www/html/database/database.sqlite
fi

# 2. Fix file permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/database/database.sqlite

# 3. Create storage symlink if not exists
php artisan storage:link --force || true

# 4. Run migrations safely
php artisan migrate --force || true

# 5. Cache configurations for maximum performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Laravel Application Ready!"

# Execute original command
exec "$@"
