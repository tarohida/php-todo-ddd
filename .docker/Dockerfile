FROM php:8.5.9-fpm-bookworm

LABEL org.opencontainers.image.authors="Taro Hida <sk8trou@gmail.com>"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libpq-dev \
        libzip-dev \
        unzip \
    && pecl install xdebug-3.5.3 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j"$(nproc)" zip pgsql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.10.2 /usr/bin/composer /usr/local/bin/composer
