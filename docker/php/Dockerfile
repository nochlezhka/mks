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

USER 1000:1000
ENV HOME=/home/nochlezhka \
    USER=nochlezhka \
    XDG_RUNTIME_DIR=/run/user/1000

COPY files/homeless /var/www/symfony
COPY files/symfony.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/symfony

EXPOSE 9000
