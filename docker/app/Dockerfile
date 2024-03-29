FROM php:8.2.4-fpm-alpine

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
      curl \
      gettext \
      icu-data-full \
      icu-dev \
      icu-libs \
      libpng-dev \
      libreoffice \
      libzip-dev \
      nginx \
      nodejs \
      yarn \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && composer self-update 2.5.1 \
 && docker-php-ext-install \
      bcmath \
      gd \
      intl \
      opcache \
      pdo_mysql \
      zip \
 \
 && touch /run/nginx.pid \
 && mkdir -p /var/cache/nginx /etc/nginx /var/log/nginx /var/www/symfony/vendor /var/lib/nginx /var/log/php8 \
 && chown -R 1000:1000 /var/www /var/cache/nginx /etc/nginx /var/log/nginx /run/nginx.pid /var/lib/nginx /var/log/php8 \
 && chmod -R 755 /var/www \
 && rm -f /etc/nginx/conf.d/default.conf

USER 1000:1000
ENV HOME=/home/nochlezhka \
    USER=nochlezhka \
    XDG_RUNTIME_DIR=/run/user/1000 \
    APP_ENV=prod

COPY --chown=nochlezhka shared/homeless /var/www/symfony
COPY --chown=nochlezhka docker/app/files/php/symfony.ini /usr/local/etc/php/conf.d/
COPY --chown=nochlezhka docker/app/files/nginx /etc/nginx/
RUN chmod +x /etc/nginx/entrypoint.sh

WORKDIR /var/www/symfony

#
# Install PHP dependencies
#
RUN composer install --no-scripts --no-dev --optimize-autoloader \
 && yarn install \
 && yarn cache clean
RUN chmod +x ./bin/console \
 && ./bin/console ckeditor:install \
 && ./bin/console assets:install --symlink \
 && yarn encore prod
