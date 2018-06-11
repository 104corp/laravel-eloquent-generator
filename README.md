# Laravel Eloquent Generator

[![Build Status](https://travis-ci.org/104corp/laravel-eloquent-generator.svg?branch=master)](https://travis-ci.org/104corp/laravel-eloquent-generator)

Laravel Eloquent Generator

## System Requirement

* PHP 7.1+
* [Laravel 5.6 requirement](https://laravel.com/docs/5.6/installation#server-requirements)
* PDO driver

## Support Database

* MySQL

## Installation

Clone this repository and execute command:

```bash
make
```

The `make` command will generate `eloquent-generator.phar` file which is executable. You can use this file or move into `/usr/local/bin`:

```bash
sudo mv eloquent-generator.phar /usr/local/bin
```

## Usage

In the Laravel Project, you can run this command directly:

```bash
cd /path/to/your-laravel-project
eloquent-generator
```

It's will generate model code into `build` directory. Use the `--output-dir` option can change output dir. If want to change namespace, Use the `--namespace` option.

In the other framework but using Eloquent ORM library, you must provide config file like laravel project.

This command using [`hassankhan/config`](https://github.com/hassankhan/config) to load config file like PHP, JSON, YAML, etc. Use `--config-file` option to specify custom config.
