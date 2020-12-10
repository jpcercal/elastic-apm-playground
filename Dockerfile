FROM php:7.3-cli

ENV XDEBUG_VERSION=2.9.4

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y \
        git \
        zip \
        unzip \
        libpq-dev

RUN pecl install xdebug-${XDEBUG_VERSION} && \
    docker-php-ext-enable xdebug

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql

RUN php -r "readfile('http://getcomposer.org/installer');" | \
    php -- --install-dir=/usr/local/bin/ --filename=composer

RUN curl -LsS https://github.com/elastic/apm-agent-php/releases/download/v1.0.0-beta1/apm-agent-php_1.0.0-beta1_all.deb -o apm-agent-php.deb && \
    dpkg -i apm-agent-php.deb

WORKDIR /var/www/app

ADD ./elastic.ini /usr/local/etc/php/conf.d/99-elastic.ini
ADD ./ /var/www/app

RUN composer install

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "web" ]
