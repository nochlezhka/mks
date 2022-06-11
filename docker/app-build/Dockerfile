FROM node:lts-alpine

COPY shared/homeless /var/homeless
RUN cd /var/homeless && yarn install && yarn encore prod

FROM php:7.4.29-fpm-alpine

USER root

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN composer self-update 2.2.9

RUN apk --no-cache add icu-dev libzip-dev
RUN docker-php-ext-install pdo_mysql bcmath intl zip

COPY shared/homeless/symfony.ini /usr/local/etc/php/conf.d
COPY --from=0 /var/homeless/web/_assets /var/www/symfony/web/_assets
COPY shared/homeless /var/www/symfony
VOLUME /var/www/symfony
RUN chown -R www-data:www-data /var/www/symfony/

USER www-data

WORKDIR /var/www/symfony
RUN composer install
RUN ./app/console fos:js-routing:dump
RUN ./app/console ckeditor:install
RUN ./app/console assets:install --symlink


