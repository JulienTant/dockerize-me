FROM ubuntu:16.04

MAINTAINER Julien Tant <julien@craftyx.fr>

RUN apt-get update \
    && apt-get install --no-install-recommends -y software-properties-common locales supervisor \
    && apt-get update \
    && locale-gen en_US.UTF-8

ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

RUN add-apt-repository ppa:nginx/stable \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install --no-install-recommends -y \
        nginx \
        php{#PHP_VERSION#}-fpm \
        php{#PHP_VERSION#}-cli \
        php{#PHP_VERSION#}-xdebug \
        php{#PHP_VERSION#}-pdo \
        php{#PHP_VERSION#}-pdo-mysql \
        php{#PHP_VERSION#}-sqlite3 \
        php{#PHP_VERSION#}-xml \
        php{#PHP_VERSION#}-mbstring \
        php{#PHP_VERSION#}-tokenizer \
        php{#PHP_VERSION#}-zip \
        php{#PHP_VERSION#}-mcrypt \
        php{#PHP_VERSION#}-gd \
        php{#PHP_VERSION#}-curl \
        curl \
    && mkdir /run/php \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get remove -y --purge software-properties-common curl \
    && apt-get clean \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY default /etc/nginx/sites-enabled/default
COPY php-fpm.conf /etc/php/{#PHP_VERSION#}/fpm/php-fpm.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir /tmp/certgen
WORKDIR /tmp/certgen
RUN openssl genrsa -des3 -passout pass:x -out server.pass.key 2048 \
    && openssl rsa -passin pass:x -in server.pass.key -out server.key \
    && rm server.pass.key \
    && openssl req -new -key server.key -out server.csr -subj "/CN=anycn.com" \
    && openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt \
    && cp server.crt /etc/ssl/certs/ \
    && cp server.key /etc/ssl/private/ \
    && rm -rf /tmp/certgen



EXPOSE 80
EXPOSE 443

WORKDIR /var/www/html

CMD ["supervisord"]
