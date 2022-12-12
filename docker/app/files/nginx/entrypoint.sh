#!/bin/sh
#
# Prepare additional configuration file and run nginx
#

set -ue

# File creation mask
umask 0027

opt_conf_file=/etc/nginx/conf.d/opts.conf
conf_file=/etc/nginx/conf.d/www.conf

cat << _EOF_ >> $opt_conf_file
fastcgi_param HTTPS ${NGINX_HTTPS:-off};
_EOF_

if [ ${NGINX_HTTPS:-off} == on ]; then
  export return="return 301 https://\$host\$request_uri;"
fi

envsubst '${return}' < /etc/nginx/conf.d/www.conf.tpl > /etc/nginx/conf.d/www.conf

#
# start nginx
#
/usr/sbin/nginx
