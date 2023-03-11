#!/bin/sh

set -ue

MODE=${MODE:-http}
DOMAIN=${DOMAIN:-_}

#
# Choose nginx mode
#
umask 0027
if [ "${MODE}" = "http" ]; then
  envsubst '${DOMAIN}' < /etc/nginx/templates/http/default.conf.tpl > /etc/nginx/conf.d/default.conf
fi

if [ "${MODE}" = "https_init" ]; then
  envsubst '${DOMAIN}' < /etc/nginx/templates/https/init.conf.tpl > /etc/nginx/conf.d/default.conf
fi

if [ "${MODE}" = "https" ]; then
  envsubst '${DOMAIN}' < /etc/nginx/templates/https/default.conf.tpl > /etc/nginx/conf.d/default.conf
  cp /etc/nginx/templates/https/options-ssl-nginx.conf /etc/nginx/conf.d/
fi

#
# Run nginx
#
while :
do
    sleep 6h
    /usr/sbin/nginx -s reload
done &
/usr/sbin/nginx




