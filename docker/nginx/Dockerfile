FROM nochlezhka/mks-php:rc-0.1.0 as php

FROM nginx:1.22-alpine

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

RUN touch /run/nginx.pid \
 && chown -R 1000:1000 /var/cache/nginx /etc/nginx /var/log/nginx /run/nginx.pid \
 && rm /etc/nginx/conf.d/default.conf

USER 1000:1000
ENV HOME=/home/nochlezhka \
    USER=nochlezhka \
    XDG_RUNTIME_DIR=/run/user/1000

COPY --chown=nochlezhka files/nginx /etc/nginx/
COPY --from=php --chown=nochlezhka /var/www/symfony /var/www/symfony

ENTRYPOINT ["nginx"]

EXPOSE 80
EXPOSE 443
