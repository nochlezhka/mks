FROM php:7.4.29-fpm-alpine

USER root

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN composer self-update 2.2.9

RUN apk --no-cache add icu-dev
RUN docker-php-ext-install pdo_mysql bcmath intl

ADD symfony.ini /usr/local/etc/php/conf.d

WORKDIR /var/www/symfony

