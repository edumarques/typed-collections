#!/bin/sh

composer install

exec php-fpm
