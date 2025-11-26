FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev openssl \
    && docker-php-ext-install pdo_mysql mbstring gd xml


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html

COPY . .


RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN php artisan jwt:secret --force || true

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
