FROM php:8.2.0-fpm-alpine

USER root

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN composer self-update 2.2.9

RUN apk --no-cache add icu-dev libzip-dev libreoffice libpng libpng-dev autoconf build-base linux-headers
RUN docker-php-ext-install pdo_mysql bcmath intl zip gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apk add --no-cache nodejs yarn

ADD symfony.ini /usr/local/etc/php/conf.d
ADD xdebug.ini /usr/local/etc/php/conf.d

WORKDIR /var/www/symfony

