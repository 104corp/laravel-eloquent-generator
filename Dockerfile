FROM php:7.1-alpine

RUN mkdir -p /source
WORKDIR /source
COPY ./eloquent-generator.phar .

ENTRYPOINT ["php", "eloquent-generator.phar"]
CMD ["--"]
