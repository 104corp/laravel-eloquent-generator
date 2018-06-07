#!/usr/bin/make -f

INSTALL_PATH := /usr/local/bin/eloquent-generator

.PHONY: all clean clean-all image test install
.DEFAULT_GOAL := all

# ------------------------------------------------------------------------------

all: clean eloquent-generator.phar

clean:
	@echo ">>> Clean artifacts ..."
	@rm -f eloquent-generator.phar

clean-all: clean
	@echo ">>> Clean all of build files ..."
	@rm -f ./composer.lock
	@rm -f ./composer.phar
	@rm -rf ./vendor

test: composer.phar
	@echo ">>> Run tests ..."
	@php composer.phar install --quiet
	@php vendor/bin/phpcs

eloquent-generator.phar: vendor test
	@echo ">>> Building phar ..."
	@php composer.phar install --quiet --no-dev --optimize-autoloader
	@php -d phar.readonly=off ./scripts/build
	@chmod +x eloquent-generator.phar
	@echo ">>> Build phar finished."

install:
	mv eloquent-generator.phar ${INSTALL_PATH}

image: eloquent-generator.phar
	docker build -t eloquent-generator .

vendor: composer.phar
	@php composer.phar install --prefer-dist

composer.phar:
	@curl -sS https://getcomposer.org/installer | php
