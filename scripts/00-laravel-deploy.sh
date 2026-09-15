#!/usr/bin/env bash
echo "Linking storage..."
php artisan storage:link

echo "Clearing stale caches..."
php artisan config:clear
php artisan route:clear

echo "Running migrations..."
php artisan migrate --force

echo "Seeding FAQ knowledge base (skips if already seeded)..."
php artisan db:seed --class='Database\Seeders\FaqDatabaseSeeder' --force

if [ -n "$ADMIN_EMAIL" ]; then
    echo "Promoting $ADMIN_EMAIL to admin..."
    php artisan app:make-admin "$ADMIN_EMAIL"
fi

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache