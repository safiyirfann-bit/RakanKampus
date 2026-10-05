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

echo "Adding question translations (EN / ZH / TA)..."
php artisan db:seed --class=KbTranslationSeeder --force

echo "Tagging chat questions with their knowledge-base topic (in the background)..."
# This checks every untagged student message against the whole knowledge base, which gets
# slow as messages pile up. Run it in the background, capped at 10 minutes, so it can never
# hold up the deploy (Render fails a deploy whose server isn't up in time).
(timeout 600 php artisan chat:tag-topics > /dev/null 2>&1 &) || true

echo "Linking storage..."
php artisan storage:link

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache