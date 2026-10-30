# syntax=docker/dockerfile:1
ARG FRANKENPHP_VERSION=1.13.1
ARG PHP_VERSION=8.5
FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION} AS base
WORKDIR /app
COPY --from=ghcr.io/php/pie:bin /pie /usr/local/bin/pie
RUN install-php-extensions gd imagick intl zip pdo_pgsql redis apcu \
    && pie install thomas-0816/pdo-duckdb-php:2.0.0.0-beta2 \
    && rm /usr/local/bin/pie \
    && php -r '$p = new PDO("duckdb::memory:"); $p->exec("INSTALL httpfs; LOAD httpfs"); echo $p->query("SELECT version()")->fetchColumn();'
COPY --chmod=755 docker/predeploy.sh /usr/local/bin/bench-predeploy
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

FROM base AS build
RUN install-php-extensions @composer
ENV COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=prod APP_DEBUG=0
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN export APP_SECRET="$(php -r 'echo bin2hex(random_bytes(32));')" \
    && composer dump-autoload --classmap-authoritative --no-dev --no-scripts \
    && php -d memory_limit=-1 bin/console cache:clear \
    && php bin/console assets:install public \
    && php bin/console importmap:install \
    && php -d memory_limit=-1 bin/console asset-map:compile

FROM base AS prod
ENV APP_ENV=prod APP_DEBUG=0
COPY --from=build /app /app
EXPOSE 80
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
