FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY public/ /var/www/html/
COPY app/ /var/www/app/
COPY db/ /var/www/html/db/

# Buat symlink supaya seed_users.php bisa resolve ../../app/config.php
# /var/www/html/db/../../app = /var/www/app ✓

EXPOSE 80
