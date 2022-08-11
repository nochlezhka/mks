FROM php:5.6-fpm-alpine

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

RUN apk update \
 && apk add --no-cache nodejs yarn curl icu-dev libzip-dev libreoffice libpng-dev \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && composer self-update 2.2.9 \
 && mkdir -p /var/www/symfony/vendor \
 && chown -R nochlezhka /var/www \
 && chmod 777 /var/www

USER 1000:1000
ENV HOME=/home/nochlezhka \
    USER=nochlezhka \
    XDG_RUNTIME_DIR=/run/user/1000

COPY --chown=nochlezhka files/homeless /var/www/symfony
COPY --chown=nochlezhka files/symfony.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/symfony
RUN composer install --no-scripts \
 && yarn install \
 && ./app/console fos:js-routing:dump \
 && ./app/console ckeditor:install \
 && ./app/console assets:install --symlink \
 && yarn encore prod

EXPOSE 9000
