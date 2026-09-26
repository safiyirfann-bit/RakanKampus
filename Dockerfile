# Stage 1: bina asset frontend (Vite + Tailwind)
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: bina app PHP sebenar
FROM richarvey/nginx-php-fpm:3.1.6

# bcmath diperlukan oleh minishlink/web-push (untuk pengiraan VAPID/JWT).
# Base image ni tak include bcmath/gmp by default, jadi tanpa ni setiap
# cubaan hantar push notification akan throw ErrorException (500).
RUN docker-php-ext-install bcmath

# GD needs webp support so .webp profile photo uploads don't fail with
# "Could not process the uploaded image". Earlier attempts here assumed
# the base image already had libwebp installed (its own Dockerfile lists
# libwebp-dev) and just tried to point GD's configure at it — but the
# diagnostic listing below came back completely empty on this image tag,
# meaning libwebp genuinely isn't there at all. `checking for libwebp...
# yes` in the previous build's log was misleading: with WEBP_CFLAGS/
# WEBP_LIBS set explicitly, configure trusts those paths without
# verifying the files exist, and only the later link/build test caught
# that libwebp.so was never actually present. So: actually install it.
RUN apk add --no-cache libwebp libwebp-dev

# poppler-utils gives `pdftotext`, used by App\Services\TimetablePdfParser to read
# the official PDF class timetable exactly (no AI) in the timetable AI capture.
RUN apk add --no-cache poppler-utils

RUN echo "--- libwebp files on this image ---" \
    && (find / -iname "libwebp*" -o -iname "*webp*.pc" 2>/dev/null | grep -v proc || true) \
    && echo "-----------------------------------"

ENV WEBP_CFLAGS="-I/usr/include"
ENV WEBP_LIBS="-L/usr/lib -lwebp"
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY . .
RUN composer install --no-dev --working-dir=/var/www/html --optimize-autoloader --no-interaction
COPY --from=assets /app/public/build ./public/build

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
# Matches nginx's fastcgi_read_timeout (conf/nginx/nginx-site.conf) — the AI
# timetable capture now makes several Groq calls one at a time (a rate-limit
# fix) and can legitimately run longer than PHP's usual default, so it needs
# raising here too or PHP would kill the script with its own timeout before
# nginx's ever came into play.
ENV PHP_MAX_EXECUTION_TIME=240

EXPOSE 80

CMD ["/start.sh"]