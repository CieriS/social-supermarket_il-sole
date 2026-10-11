FROM php:8.3-apache

RUN docker-php-ext-install mysqli

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
