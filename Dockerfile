FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    autoconf \
    g++ \
    make \
    linux-headers \
    pcre-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        opcache \
        sockets \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    # Удаляем тяжёлые build-инструменты
    && apk del autoconf g++ make pcre-dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

COPY .docker/supervisord.conf /etc/supervisord.conf

RUN chown -R www-data:www-data storage bootstrap/cache \
    && composer dump-autoload --optimize

EXPOSE 9000

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
