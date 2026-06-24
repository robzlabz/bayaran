FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx supervisor curl git unzip libzip-dev postgresql-dev \
    oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev nodejs npm

RUN docker-php-ext-install pdo pdo_pgsql mbstring zip gd bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build && rm -rf node_modules
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Config files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
