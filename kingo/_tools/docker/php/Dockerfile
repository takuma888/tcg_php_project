FROM php:7.0-fpm

RUN apt-get update \
    && apt-get install -y \
        autoconf \
        g++ \
        make \
        openssl \
        libssl-dev \
        libcurl4-openssl-dev \
        pkg-config \
        libsasl2-dev

RUN pecl install redis-3.1.0 \
    && pecl install mongodb-1.2.9 \
    && pecl install apcu-5.1.8

RUN docker-php-ext-enable redis mongodb apcu

RUN docker-php-ext-install pdo_mysql pcntl posix


RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd


RUN docker-php-ext-enable opcache

RUN apt-get update \
    && apt-get install -y libevent-dev

RUN docker-php-ext-install sockets

RUN pecl install event
RUN docker-php-ext-enable event

RUN apt-get update \
    && apt-get install -y libgmp-dev

RUN docker-php-ext-install gmp


COPY www.conf /usr/local/etc/php-fpm.d/

EXPOSE 9000 8282