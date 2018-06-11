FROM php:7.1-alpine

RUN docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) pdo_mysql

COPY ./eloquent-generator.phar /usr/local/bin/

WORKDIR /source

ENTRYPOINT ["php", "/usr/local/bin/eloquent-generator.phar"]
CMD ["--"]
