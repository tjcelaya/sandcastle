FROM php:7.1-fpm-alpine

RUN apk add --no-cache \
     libmcrypt \
     mysql-client \
     curl \
     icu-dev \
     libmcrypt-dev \
     libxml2-dev \
     g++ \
     make \
     autoconf \
  && docker-php-source extract \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && docker-php-source delete \
  && docker-php-ext-install mcrypt soap pdo_mysql intl \
  && apk del \
     g++ \
     make \
     autoconf

RUN mkdir /app
RUN rmdir /var/www/html && ln -s /app/public /var/www/html

WORKDIR /app

# an example of installing one of the extensions from source follows, but isn't within our scope

# ENV PHPREDIS_VERSION 3.1.2

# RUN docker-php-source extract \
#     && curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz \
#     && tar xfz /tmp/redis.tar.gz \
#     && rm -r /tmp/redis.tar.gz \
#     && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis \
#     && docker-php-ext-install redis \
#     && docker-php-source delete

# RUN pecl install redis-3.1.2 \
#     && docker-php-ext-enable redis
