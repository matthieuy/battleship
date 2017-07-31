Nginx configuration
===================

This is a example of nginx configuration

I use this environment :
- Domain : `battleship.domain.org`
- Workdir : `/home/battleship`
- Port : `443` SSL/TLS (with let's encrypt certificats)

```
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name battleship.domain.org;
    client_max_body_size 1m;

    root /home/battleship/web;
    index app.php;

    # SSL / TLS
    ssl on;
    ssl_certificate /etc/letsencrypt/live/battleship.domain.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/battleship.domain.org/privkey.pem;
    ssl_dhparam /etc/ssl/private/dh4096.pem;
    ssl_prefer_server_ciphers On;
    ssl_protocols TLSv1.2;
    ssl_ciphers "EECDH+AES:+AES128:+AES256:+SHA";

    # Log
    access_log  /home/battleship/var/logs/nginx.access.log;
    error_log   /home/battleship/var/logs/nginx.error.log;

    location / {
        try_files $uri /app.php$is_args$args;
        autoindex off;
        index app.php;
    }

    location ~ \.php$ {
        fastcgi_index app.php;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        try_files $uri $uri/ /app.php$is_args$args;
        include /etc/nginx/fastcgi_params;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        fastcgi_param APP_ENV prod;
    }
    
    sendfile off;
}
```
