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

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache