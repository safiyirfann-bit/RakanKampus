#!/usr/bin/env bash
echo "Linking storage..."
php artisan storage:link

echo "Clearing stale caches..."
php artisan config:clear
php artisan route:clear

echo "Running migrations..."
php artisan migrate --force

echo "Importing campus datasets into the knowledge base..."
php artisan db:seed --class=CampusDatasetSeeder --force

echo "Tagging chat questions with their knowledge-base topic..."
php artisan chat:tag-topics || true

echo "Linking storage..."
php artisan storage:link

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache