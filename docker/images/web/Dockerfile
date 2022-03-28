FROM php:5.4-apache

RUN a2enmod rewrite
RUN service apache2 restart

RUN pecl install xdebug-2.4.1 && docker-php-ext-enable xdebug
RUN docker-php-ext-install mysql
