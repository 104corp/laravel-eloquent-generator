# Laravel Eloquent Generator

[![Build Status](https://travis-ci.com/104corp/laravel-eloquent-generator.svg?branch=master)](https://travis-ci.com/104corp/laravel-eloquent-generator)
[![GitHub Release](https://img.shields.io/github/release/104corp/laravel-eloquent-generator.svg)](https://github.com/104corp/laravel-eloquent-generator/releases)
[![](https://img.shields.io/github/downloads/104corp/laravel-eloquent-generator/total.svg)](https://github.com/104corp/laravel-eloquent-generator/releases)
[![codecov](https://codecov.io/gh/104corp/laravel-eloquent-generator/branch/master/graph/badge.svg)](https://codecov.io/gh/104corp/laravel-eloquent-generator)
[![](https://img.shields.io/docker/stars/104corp/eloquent-generator.svg)](https://hub.docker.com/r/104corp/eloquent-generator/)
[![](https://img.shields.io/docker/pulls/104corp/eloquent-generator.svg)](https://hub.docker.com/r/104corp/eloquent-generator/)

Laravel Eloquent Generator

* [中文](https://github.com/104corp/laravel-eloquent-generator/wiki/%E4%B8%AD%E6%96%87)

## System Requirement

* PHP 7.2+
* PDO driver

The code generated can use in following Laravel version:

* Laravel 4.0 ~ 6.x

## Support Database

* MySQL
* SQLite
* PostgreSQL

## Installation

Download the [Release](https://github.com/104corp/laravel-eloquent-generator/releases) phar file and execute it:

```bash
chmod +x eloquent-generator.phar
./eloquent-generator.phar
```

Or move into `/usr/local/bin`:

```bash
mv eloquent-generator.phar /usr/local/bin/eloquent-generator
eloquent-generator
```

## Usage

In the Laravel project, you can run this command directly:

```bash
cd /path/to/your-laravel-project
eloquent-generator
```

It's will generate model code into `build` directory, use the `--output-dir` option can change output dir. If want to change namespace, use the `--namespace` option.

In the other framework but using Eloquent ORM library, you must provide config file like laravel project.

This command using [`hassankhan/config`](https://github.com/hassankhan/config) to load config file like PHP, JSON, YAML, etc. Use `--config-file` option to specify custom config.

If only want build one connection, use the `--connection` option to specify.

Use the `--overwrite` option if you want to overwrite exist code.

## Using Docker

> See the info about Docker at [DockerHub](https://hub.docker.com/r/104corp/eloquent-generator/).

Just like using phar, you can run this command in the Laravel project:

```bash
cd /path/to/your-laravel-project
docker run -it --rm -v `pwd`:/source 104corp/eloquent-generator
```

Or set the alias will more like phar:

```bash
alias eloquent-generator='docker run -it --rm -v $PWD:/source 104corp/eloquent-generator'
eloquent-generator
```

See more info about option at [Usage](#Usage) section.

## Build Yourself

Clone this repository and execute command:

```bash
make
```

The `make` command will generate `eloquent-generator.phar` file which is executable.
