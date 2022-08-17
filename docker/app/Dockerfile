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
# Setup & configure php & nginx
# NOTE: it's intentional decision that all versions are pinned,
#       they can be upgraded later,
#       but it's a good practice to know which versions we have
#
RUN apk update \
 && apk upgrade \
 && apk add --no-cache \
      nodejs=16.16.0-r0 \
      yarn=1.22.19-r0 \
      curl=7.83.1-r2 \
      icu-dev=71.1-r2	 \
      libzip-dev=1.8.0-r1 \
      libreoffice=7.2.7.2-r1 \
      libpng-dev=1.6.37-r1 \
      nginx=1.22.0-r1 \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && composer self-update 2.2.9 \
 && docker-php-ext-install gd zip bcmath intl pdo_mysql \
 \
 && touch /run/nginx.pid \
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

#
# Install PHP dependencies
#
RUN composer install --no-scripts \
 && yarn install \
 && yarn cache clean
RUN ./app/console fos:js-routing:dump \
 && ./app/console ckeditor:install \
 && ./app/console assets:install --symlink \
 && yarn encore prod \
 && php vendor/sensio/distribution-bundle/Resources/bin/build_bootstrap.php
