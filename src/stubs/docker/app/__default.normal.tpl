server {
    error_log /dev/stdout info;
    access_log /dev/stdout;

    listen 80;
    listen [::]:80 default_server ipv6only=on;

    listen 443 ssl;
    ssl_certificate      /etc/ssl/certs/server.crt;
    ssl_certificate_key  /etc/ssl/private/server.key;


    root /var/www/html;
    index index.php index.html index.htm;

    server_name _;

    location = /favicon.ico { log_not_found off; access_log off; }
    location = /robots.txt  { log_not_found off; access_log off; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php<?=$phpVersion?>-fpm.sock;
    }
}
