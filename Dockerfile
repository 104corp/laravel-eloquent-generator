FROM 104corp/php-testing:7.3 AS builder

ARG VERSION=dev-master

RUN apk add --no-cache make

RUN mkdir -p /source
WORKDIR /source

COPY composer.json .
RUN composer install

COPY . .

RUN php vendor/bin/phpcs
RUN php vendor/bin/phpunit

RUN make eloquent-generator.phar VERSION=${VERSION}

FROM php:7.3-alpine

RUN docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) pdo_mysql

COPY --from=builder /source/eloquent-generator.phar /usr/local/bin/

WORKDIR /source

ENTRYPOINT ["php", "/usr/local/bin/eloquent-generator.phar"]
CMD ["--"]
