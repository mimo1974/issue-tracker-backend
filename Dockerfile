FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-progress \
    --prefer-dist

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && composer run-script post-install-cmd --no-dev

FROM caddy:2-alpine AS caddy

FROM php:8.4-fpm AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        opcache \
        pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.memory_consumption=256'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY --from=caddy /usr/bin/caddy /usr/bin/caddy
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV APP_ENV=prod

WORKDIR /var/www/html

COPY --from=vendor /app ./

RUN chown -R www-data:www-data var

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]

FROM app AS dev

ENV APP_ENV=dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer dump-autoload

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini
