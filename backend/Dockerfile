FROM php:8.2-fpm-alpine

# Argumentos de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    postgresql-dev \
    oniguruma-dev

# Instalar extensões PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    xml \
    zip \
    bcmath \
    mbstring \
    opcache \
    intl

# Instalar Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Configurar OPcache para produção
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar grupo e usuário não-root
RUN addgroup -g ${GROUP_ID} laravel && \
    adduser -D -u ${USER_ID} -G laravel laravel

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY --chown=laravel:laravel . .

# Instalar dependências do Composer (apenas em build)
RUN composer install --no-interaction --optimize-autoloader --no-dev || true

# Copiar entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Configurar permissões
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Mudar para usuário não-root
USER laravel

# Expor porta PHP-FPM
EXPOSE 9000

# Usar entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["php-fpm"]
