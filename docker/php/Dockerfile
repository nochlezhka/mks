FROM codepilotscom/mks-php:5.6-fpm

ARG UID
ARG GUID

RUN usermod -u $UID www-data
RUN groupmod -g $GUID www-data

ADD symfony.ini /usr/local/etc/php/conf.d

RUN usermod -u $UID www-data -d /var/www/symfony

WORKDIR /var/www/symfony

EXPOSE 9000

