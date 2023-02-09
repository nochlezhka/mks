FROM mks_humaid_app:latest

FROM codepilotscom/mks-nginx:3.4

MAINTAINER Vincent Composieux <vincent.composieux@gmail.com>

ADD nginx.conf /etc/nginx/
ADD www.conf /etc/nginx/conf.d/
ADD common-php.inc /etc/nginx/conf.d/

ARG UID
ARG GUID
RUN usermod -u $UID www-data
RUN groupmod -g $GUID www-data
RUN chown -R www-data:www-data /var/lib/nginx

COPY --from=0 /app/public /var/www/humaid/public

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
EXPOSE 60050
