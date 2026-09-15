#!/usr/bin/env bash
echo "Running composer install..."
composer install --no-dev --working-dir=/var/www/html --optimize-autoloader

echo "Linking storage..."
php artisan storage:link

echo "Clearing stale caches..."
php artisan config:clear
php artisan route:clear

echo "Running migrations..."
php artisan migrate --force

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache