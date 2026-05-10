FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql

RUN a2enmod rewrite

COPY . /var/www/html/

WORKDIR /var/www/html

EXPOSE 80