FROM 104corp/php-testing:5.5

ENV SOURCE_ROOT /source

# Set directory
RUN mkdir -p ${SOURCE_ROOT}
WORKDIR ${SOURCE_ROOT}

# Copy composer.json
COPY composer.json .

# Install packages without cache
RUN set -xe && \
        php -n ${COMPOSER_PATH} install && \
        composer clear-cache

# Dapper env
ENV DAPPER_SOURCE ${SOURCE_ROOT}
ENV DAPPER_OUTPUT ./build ./vendor ./composer.lock

ENTRYPOINT ["./scripts/entry"]
CMD ["ci"]
