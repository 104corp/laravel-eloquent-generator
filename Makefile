#!/usr/bin/make -f

.PHONY: all clean clean-all image
.DEFAULT_GOAL := all

# ------------------------------------------------------------------------------

all: clean eloquent-generator.phar

clean:
	rm -f eloquent-generator.phar

clean-all: clean
	rm -f ./composer.lock
	rm -f ./composer.phar
	rm -rf ./vendor

eloquent-generator.phar: vendor
	@php composer.phar install --no-dev --optimize-autoloader
	@php -d phar.readonly=off ./scripts/build
	@chmod +x eloquent-generator.phar

image: eloquent-generator.phar
	docker build -t eloquent-generator .

vendor: composer.phar
	@php composer.phar install --prefer-dist

composer.phar:
	@curl -sS https://getcomposer.org/installer | php
