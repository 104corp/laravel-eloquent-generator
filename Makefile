#!/usr/bin/make -f

INSTALL_PATH:=/usr/local/bin/eloquent-generator
DOCKER_IMAGE:=104corp/eloquent-generator
VERSION:=master
TARGETS:=ci coverage

.PHONY: all clean clean-all image test install sqlite container

# ------------------------------------------------------------------------------

all: clean test eloquent-generator.phar

clean:
	@echo ">>> Clean artifacts ..."
	@rm -f ./eloquent-generator.phar
	@rm -rf ./build

clean-all: clean
	@echo ">>> Clean all of build files ..."
	@rm -f ./composer.lock
	@rm -f ./composer.phar
	@rm -rf ./vendor

sqlite:
	@sqlite3 tests/Fixture/sqlite.db < tests/Fixture/sqlite.sql

container:
	@docker-compose down -v
	@docker-compose up -d
	@docker-compose logs -f

test: composer.phar
	@echo ">>> Run tests ..."
	@php composer.phar install --quiet
	@php vendor/bin/phpcs

eloquent-generator.phar: vendor
	@echo ">>> Building phar ..."
	@./scripts/bump-version ${VERSION}
	@php composer.phar install --quiet --no-dev --optimize-autoloader
	@php -d phar.readonly=off ./scripts/build
	@chmod +x eloquent-generator.phar
	@echo ">>> Build phar finished."

install:
	mv eloquent-generator.phar ${INSTALL_PATH}

image: eloquent-generator.phar
	docker build -t ${DOCKER_IMAGE} .

vendor: composer.phar
	@php composer.phar install --prefer-dist

composer.phar:
	@curl -sS https://getcomposer.org/installer | php

$(TARGETS): .dapper
	./.dapper $@

.dapper:
	@echo ">>> Downloading dapper ..."
	@curl -sL https://releases.rancher.com/dapper/latest/dapper-`uname -s`-`uname -m` > .dapper.tmp
	@@chmod +x .dapper.tmp
	@./.dapper.tmp -v
	@mv .dapper.tmp .dapper
