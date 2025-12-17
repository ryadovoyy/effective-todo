FROM php:8.5-fpm

RUN apt-get update && apt-get install --no-install-recommends -y \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www

COPY --chown=www composer.* .

RUN composer install --no-autoloader

COPY --chown=www . .

RUN composer dump-autoload

USER www

EXPOSE 9000
