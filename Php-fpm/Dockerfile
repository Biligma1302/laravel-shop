FROM dh-mirror.gitverse.ru/library/php:8.4-fpm

RUN apt update -q;
RUN apt install -q -y libpq-dev;
RUN docker-php-ext-install pdo_pgsql pgsql;

RUN pecl channel-update pecl.php.net \
    && pecl install xdebug-3.4.1 \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear;

COPY --from=dh-mirror.gitverse.ru/library/composer:latest /usr/bin/composer /usr/bin/composer

ARG UID
ARG GID

RUN apt-get update && apt-get install -y shadow-utils || true && \
    groupmod -o -g ${GID} www-data && \
    usermod -o -u ${UID} -g www-data www-data

RUN chown -R www-data:www-data /var/log /var/lib/php || true

USER www-data
