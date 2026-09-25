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

# Base image install libwebp-dev tapi configure GD tanpa --with-webp, so
# imagecreatefromstring() gagal senyap untuk fail .webp (profile photo
# upload return "Could not process the uploaded image"). libwebp-dev dah
# ada, tapi first attempt kat sini gagal build sebab pkg-config binary tu
# sendiri takde dalam base image (GD's --with-webp detection guna
# pkg-config untuk cari libwebp) — so kena install `pkgconf` (nama
# package pkg-config kat Alpine) dulu sebelum configure.
RUN apk add --no-cache pkgconf \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
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

EXPOSE 80

CMD ["/start.sh"]