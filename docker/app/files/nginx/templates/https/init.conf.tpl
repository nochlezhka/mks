server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name ${DOMAIN} www.${DOMAIN};

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
}

