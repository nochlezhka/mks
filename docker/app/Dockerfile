FROM php:7.4.29-fpm-alpine

#
# Setup Moscow time
#
RUN rm -f /etc/localtime \
 && ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime

#
# Create a base UID/GID and SUID/SGID which will be used by container
#
RUN addgroup -S --gid 1000 nochlezhka \
 && adduser -S -G nochlezhka -u 1000 -s /bin/bash nochlezhka \
 && mkdir -p /run/user/1000 \
 && chown -R nochlezhka /run/user/1000 /home/nochlezhka \
 && echo nochlezhka:100000:65536 | tee /etc/subuid | tee /etc/subgid

#
# setup software
#
RUN apk update \
 && apk upgrade \
 && apk add --no-cache nodejs yarn curl icu-dev libzip-dev libreoffice libpng-dev nginx \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && composer self-update 2.2.9 \
 && docker-php-ext-install gd zip bcmath mysqli pdo pdo_mysql

RUN touch /run/nginx.pid \
 && mkdir -p /var/cache/nginx /etc/nginx /var/log/nginx /var/www/symfony/vendor /var/lib/nginx /var/log/php7 \
 && chown -R 1000:1000 /var/www /var/cache/nginx /etc/nginx /var/log/nginx /run/nginx.pid /var/lib/nginx /var/log/php7 \
 && chmod -R 755 /var/www \
 && rm -f /etc/nginx/conf.d/default.conf

USER 1000:1000
ENV HOME=/home/nochlezhka \
    USER=nochlezhka \
    XDG_RUNTIME_DIR=/run/user/1000

COPY --chown=nochlezhka files/homeless /var/www/symfony
COPY --chown=nochlezhka files/php/symfony.ini /usr/local/etc/php/conf.d/
COPY --chown=nochlezhka files/nginx /etc/nginx/

WORKDIR /var/www/symfony

RUN composer install --no-scripts \
 && yarn install
RUN chmod -R 755 ./app \
 && ./app/console fos:js-routing:dump \
 && ./app/console ckeditor:install \
 && ./app/console assets:install --symlink \
 && yarn encore prod \
 && php vendor/sensio/distribution-bundle/Resources/bin/build_bootstrap.php
