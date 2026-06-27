FROM php:8.2-cli-alpine

RUN apk add --no-cache git curl zip unzip postgresql-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-install pdo pdo_pgsql gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
