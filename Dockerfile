FROM php:7.2-alpine

# install build environment
RUN apk add --no-cache freetype libjpeg-turbo libpng libwebp gettext icu-libs libmemcached postgresql-libs aspell-libs libzip \
    && apk add --no-cache --virtual ext-dev-dependencies $PHPIZE_DEPS binutils gettext-dev icu-dev \
        postgresql-dev cyrus-sasl-dev libxml2-dev libmemcached-dev libzip-dev \
        freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev aspell-dev \
    && export CPU_COUNT=$(cat /proc/cpuinfo | grep processor | wc -l) \
    && cd /usr/src/ \
    && docker-php-ext-install -j$CPU_COUNT bcmath gettext iconv mysqli pdo_mysql pdo_pgsql pgsql pspell zip \
# build standard extensions
    && docker-php-ext-configure gd  --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        --with-webp-dir=/usr/include/ --with-png-dir=/usr/include/   --enable-gd-native-ttf --with-zlib-dir \
    && docker-php-ext-install -j$CPU_COUNT gd \
    && docker-php-ext-enable opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl \
# build and install PECL extensions
    && pecl channel-update pecl.php.net \
    && yes no| pecl install igbinary xdebug ds \
    && pecl download redis memcached \
        && tar -xf redis* && cd redis* && phpize && ./configure --enable-redis-igbinary && make -j$CPU_COUNT && make install && cd .. \
        && tar -xf memcached* && cd memcached* && phpize && ./configure --disable-memcached-sasl --enable-memcached-igbinary && make -j$CPU_COUNT && make install && cd .. \
    && docker-php-ext-enable igbinary redis memcached ds \
# cleanup
    && apk del ext-dev-dependencies \
    && rm -rf redis* memcached* /tmp/pear \
# make the entrypoint executable
    && chmod a+x /usr/local/bin/docker-php-entrypoint \
    && mkdir /docker-entrypoint-init.d/ \
# restrict console commands execution for web scripts
    && chmod o-rx /bin/busybox /usr/bin/curl /usr/local/bin/pecl \
# add composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . /app

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181