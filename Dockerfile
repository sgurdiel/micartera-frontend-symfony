ARG BASE_OCI_IMAGE=php:8.4.19-fpm-alpine
ARG HELPER_OCI_IMAGE1=node:24.12.0-alpine

############################
# PHP extension build stage
############################
FROM ${BASE_OCI_IMAGE} AS php-ext-build
WORKDIR /var/www/html

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev; \
    pecl install opentelemetry protobuf; \
    docker-php-ext-enable opcache opentelemetry protobuf; \
    docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        pdo_mysql; \
    rm -rf /tmp/pear

############################
# Composer vendor stage
############################
FROM php-ext-build AS vendorphp
WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative

############################
# Node deps stage
############################
FROM ${HELPER_OCI_IMAGE1} AS depsnode
WORKDIR /var/www/html

COPY package*.json ./
RUN npm ci

############################
# Node build stage
############################
FROM ${HELPER_OCI_IMAGE1} AS buildnode
WORKDIR /var/www/html

COPY --from=depsnode /var/www/html/node_modules ./node_modules
COPY package*.json webpack.config.js postcss.config.js ./
COPY assets ./assets
RUN npm run build

############################
# Final runtime image
############################
FROM ${BASE_OCI_IMAGE} AS runtime
WORKDIR /var/www/html

RUN set -eux; \
    apk add --no-cache \
        icu-libs

# Copy PHP extensions and config from build stage
COPY --from=php-ext-build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=php-ext-build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy application
COPY . .
COPY --from=vendorphp /var/www/html/vendor ./vendor
COPY --from=buildnode /var/www/html/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html

USER www-data
ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 9000
CMD ["php-fpm", "-F"]
