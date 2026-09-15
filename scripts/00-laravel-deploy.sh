#!/usr/bin/env bash
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