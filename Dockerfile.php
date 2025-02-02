FROM php:7.3.33-fpm-alpine3.14 AS build

# Install required system packages and PHP extensions
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        autoconf \
        gcc \
        g++ \
        make \
        libtool \
        curl-dev \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libxml2-dev \
        oniguruma-dev \
        postgresql-dev \
        zlib-dev \
        libzip-dev \
        imagemagick-dev \
    && apk add --no-cache \
        mariadb-client \
        git \
        supervisor \
        shadow \
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
        posix \
    && pecl install imagick xdebug \
    && docker-php-ext-enable imagick xdebug \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/share/doc

# Use a new, clean image for the final stage
FROM php:7.3.33-fpm-alpine3.14

# Copy the built PHP extensions from the build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Install runtime dependencies
RUN set -ex \
    && apk add --no-cache \
        mariadb-client \
        supervisor \
        shadow \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man /usr/share/doc

# Copy any additional configuration files or scripts if needed
# COPY ./your-config-files /path/in/container

# Set up the working directory
WORKDIR /var/www/html

# Expose the necessary ports
EXPOSE 9000

# Start the PHP-FPM server
CMD ["php-fpm"]
