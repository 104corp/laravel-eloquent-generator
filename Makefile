#!/usr/bin/make -f

.PHONY: all clean-all
.DEFAULT_GOAL := all

# ------------------------------------------------------------------------------

all: vendor

clean-all:
	rm -f ./composer.lock
	rm -f ./composer.phar
	rm -rf ./vendor

vendor: composer.phar
	@php composer.phar install --prefer-dist

composer.phar:
	@curl -sS https://getcomposer.org/installer | php
