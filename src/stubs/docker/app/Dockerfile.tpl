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
        php<?=$phpVersion?>-fpm \
        php<?=$phpVersion?>-cli \
        php<?=$phpVersion?>-xdebug \
        php<?=$phpVersion?>-pdo \
        php<?=$phpVersion?>-pdo-mysql \
        php<?=$phpVersion?>-sqlite3 \
        php<?=$phpVersion?>-xml \
        php<?=$phpVersion?>-mbstring \
        php<?=$phpVersion?>-tokenizer \
        php<?=$phpVersion?>-zip \
        php<?=$phpVersion?>-mcrypt \
        php<?=$phpVersion?>-gd \
        php<?=$phpVersion?>-curl \
        curl \
    && mkdir /run/php \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
<?php if($withBlackfire): ?>
    && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > /etc/php/<?=$phpVersion?>/mods-available/blackfire.ini \
    && phpenmod blackfire \
<?php endif; ?>
    && apt-get remove -y --purge software-properties-common curl \
    && apt-get clean \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


COPY default /etc/nginx/sites-enabled/default
COPY php-fpm.conf /etc/php/<?=$phpVersion?>/fpm/php-fpm.conf
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
