FROM php:8.0-fpm-alpine

ARG USER_ID=1000
ARG XDEBUG_PORT=9003

RUN apk update --quiet && \
    apk add --quiet --no-cache bash git shadow openssh openssl-dev p7zip && \
    apk add --quiet --no-cache pcre-dev ${PHPIZE_DEPS}
RUN pecl channel-update pecl.php.net && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug > /dev/null && \
    echo "xdebug.xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=$XDEBUG_PORT" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.max_nesting_level=300" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    touch /tmp/xdebug.log && \
    chown www-data:www-data /tmp/xdebug.log && \
    apk del pcre-dev ${PHPIZE_DEPS}

RUN mkdir -p /var/www/.ssh/ && \
    touch /var/www/.ssh/known_hosts && \
    ssh-keyscan github.com >> /var/www/.ssh/known_hosts

WORKDIR .
COPY . .

COPY --chown=www-data:www-data --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN usermod -u $USER_ID www-data && chown -R www-data:www-data /var/www/ .
USER www-data

CMD ["php-fpm"]
