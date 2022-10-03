FROM php:8.1.11-fpm-alpine3.16

RUN apk add --no-cache mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu && \
    apk add --no-cache ${PHPIZE_DEPS} imagemagick imagemagick-dev && \
    apk add --no-cache --virtual build-essentials icu-dev icu-libs zlib-dev g++ make automake autoconf libzip-dev libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install intl && \
    docker-php-ext-install opcache && \
    docker-php-ext-install exif && \
    docker-php-ext-install zip && \
    docker-php-ext-install sockets && \
    docker-php-source extract && \
    apk -Uu add git rabbitmq-c-dev && \
    git clone --branch master --depth 1 https://github.com/php-amqp/php-amqp.git /usr/src/php/ext/amqp && \
    cd /usr/src/php/ext/amqp && git submodule update --init && \
    docker-php-ext-install amqp && \
    apk add --no-cache --virtual .build-deps && \
    printf "\n\n\n" | pecl install -o -f redis && \
    docker-php-ext-enable redis && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    printf "\n" | pecl install -o -f imagick && \
    docker-php-ext-enable imagick && \
    wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    apk del --purge .build-deps && \
    apk del --no-cache ${PHPIZE_DEPS} && \
    apk del build-essentials && rm -rf /usr/src/php*

# docker build -t wacdis/php:8.1.11-fpm-alpine3.16 -f ./php8.dockerfile .