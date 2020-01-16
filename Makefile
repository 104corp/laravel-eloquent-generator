#!/usr/bin/make -f

INSTALL_PATH:=/usr/local/bin/eloquent-generator
DOCKER_IMAGE:=104corp/eloquent-generator
VERSION:=dev-master
TARGETS:=ci style test coverage

.PHONY: all clean clean-all image install sqlite container

# ------------------------------------------------------------------------------

all: clean  test eloquent-generator.phar

clean:
	@echo ">>> Clean artifacts ..."
	@rm -f ./eloquent-generator.phar
	@rm -rf ./build

clean-all: clean
	@echo ">>> Clean all of build files ..."
	@rm -f ./composer.lock
	@rm -f ./composer.phar
	@rm -rf ./vendor

check:
	php vendor/bin/phpcs

coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi

test: check
	phpdbg -qrr vendor/bin/phpunit

sqlite:
	@sqlite3 tests/Fixture/sqlite.db < tests/Fixture/sqlite.sql

container:
	@docker-compose down -v
	@docker-compose up -d
	@docker-compose logs -f

eloquent-generator.phar:
	@echo ">>> Building phar ..."
	@composer install --no-dev --optimize-autoloader --quiet
	@./scripts/bump-version bump ${VERSION}
	@php -d phar.readonly=off ./scripts/build
	@chmod +x eloquent-generator.phar
	@echo ">>> Build phar finished."
	@composer install --dev --quiet

install:
	mv eloquent-generator.phar ${INSTALL_PATH}

image:
	docker build --build-arg VERSION=${VERSION} --tag ${DOCKER_IMAGE} .
