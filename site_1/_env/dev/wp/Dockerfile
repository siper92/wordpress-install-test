FROM php:8.4-fpm

ARG BUILD_USER=www

RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        unzip \
        libpq-dev \
        libonig-dev \
        libssl-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libicu-dev \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libmagickwand-dev \
        && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
        && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        mysqli \
        opcache \
        intl \
        zip \
        bcmath \
        soap \
        gd \
        exif \
        && pecl install redis xdebug imagick \
        && docker-php-ext-enable redis xdebug imagick \
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
        && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /usr/local/bin/

RUN groupadd -g 1000 ${BUILD_USER} && mkdir /home/${BUILD_USER}/ \
    && useradd -u 1000 -g 1000 -d /home/${BUILD_USER}/ -s /bin/bash ${BUILD_USER}

COPY ./wp/php.ini /usr/local/etc/php/conf.d/env_config.ini
COPY ./wp/fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY .bashrc /home/${BUILD_USER}/.bashrc
COPY .bashrc /root/.bashrc

RUN date +%d/%m/%Y-%H:%M:%S > /build-timestamp && echo $(date +%s) >> /build-timestamp

RUN chown -R ${BUILD_USER}:${BUILD_USER} /home/${BUILD_USER}/

EXPOSE 9000

USER ${BUILD_USER}
WORKDIR /app/

CMD ["php-fpm", "--nodaemonize"]