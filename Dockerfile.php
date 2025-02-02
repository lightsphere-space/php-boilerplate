FROM php:7.3.33-fpm-alpine3.14

# Install required system packages
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    icu-dev \
    zlib-dev \
    oniguruma-dev \
    libxml2-dev \
    curl-dev \
    mariadb-client \
    postgresql-dev \
    libzip-dev \
    imagemagick-dev \
    git \
    supervisor \
    shadow \
    autoconf \
    gcc \
    g++ \
    make \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include --with-jpeg-dir=/usr/include \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        pdo_pgsql \
        intl \
        zip \
        opcache \
        bcmath \
        exif \
        iconv \
        soap \
        sockets \
        mbstring \
        pcntl \
        simplexml \
        xmlrpc \
        tokenizer \
        calendar \
        sysvsem \
        sysvshm \
        sysvmsg \
        shmop \
        posix

# Install PECL extensions
RUN apk add --no-cache --virtual .build-deps \
    libtool \
    && pecl install redis imagick xdebug \
    && docker-php-ext-enable redis imagick xdebug \
    && apk del .build-deps

# Clear cache
RUN rm -rf /var/cache/apk/*

