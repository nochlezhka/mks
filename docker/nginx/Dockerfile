FROM nginx:1.21.6-alpine

MAINTAINER Vincent Composieux <vincent.composieux@gmail.com>

COPY nginx.conf /etc/nginx/
COPY www.conf /etc/nginx/conf.d/default.conf
COPY common-php.inc /etc/nginx/conf.d/

ARG UID
ARG GUID
RUN apk add --no-cache shadow
RUN adduser -u $UID -D -g '' -G www-data www-data
RUN usermod -u $UID www-data
RUN groupmod -g $GUID www-data
RUN mkdir /var/lib/nginx
RUN chown -R www-data:www-data /var/lib/nginx

EXPOSE 443
EXPOSE 60050

