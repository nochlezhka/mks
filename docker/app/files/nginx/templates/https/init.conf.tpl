server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name ${DOMAIN};

    location ^~ /.well-known/acme-challenge/ {
        allow all;
    	default_type "text/plain";
    	root /var/www/certbot;
    }
}