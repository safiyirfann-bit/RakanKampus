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
# upload return "Could not process the uploaded image"). Dua attempt lepas
# ni gagal sebab pkg-config (walaupun dah install `pkgconf` package) tetap
# tak jumpa libwebp.pc dia — so instead of terus bergantung pada
# pkg-config, terus bagi GD punya configure script CFLAGS/LIBS tu secara
# eksplisit (ni exact workaround yang error message sendiri cadangkan).
# Print listing fail libwebp sekali supaya kalau ni pun gagal, log build
# tu terus tunjuk path sebenar fail-fail tu berada kat mana.
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

EXPOSE 80

CMD ["/start.sh"]