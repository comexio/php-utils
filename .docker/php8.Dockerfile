FROM php:8.1-apache

RUN apt update && apt install -y libpq-dev libpng-dev libxml2-dev git uuid-runtime libzip-dev libonig-dev
RUN docker-php-ext-install gd xml zip mbstring sockets

RUN pecl install xdebug-3.1.0 && docker-php-ext-enable xdebug \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.mode=coverage,debug,develop" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.start_with_request=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/php.ini

WORKDIR /var/www/app

# Instalando o Composer
RUN php -r "copy('http://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN rm /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

VOLUME /var/www/app