upstream php-upstream {
    server php:9000;
}

server {
    listen 80;
    server_name ${DOMAIN};

    root /var/www/symfony/web;

    location ~* /sitemap(.*).xml {
        try_files $uri @rewrite;
    }
    location / {
        # try to serve file directly, fallback to app.php
        try_files $uri /app.php$is_args$args;
    }
    
    location ~* \.(jpg|xml|gif|swf|ico|css|zip|rar|doc|xls|js|txt|dtd|png|jpeg|eot|woff|woff2|ttf|svg|html)$ {
        access_log off;
        expires max;
    }

    # DEV
    location ~ ^/(app_dev|config|adminer)\.php(/|$) {
        fastcgi_pass php-upstream;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
    # PROD
    location ~ ^/app\.php(/|$) {
        fastcgi_pass php-upstream;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    error_log /var/log/nginx/symfony_error.log;
    access_log /var/log/nginx/symfony_access.log;
}
