FROM php:7.4-apache
ARG XDEBUG_VERSION=2.6.0

RUN apt update && apt install -y libpq-dev libpng-dev libxml2-dev git
RUN docker-php-ext-install gd xml zip mbstring

RUN pecl install xdebug-2.6.0 && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_log=/tmp/xdebug.log" >> /usr/local/etc/php/php.ini

WORKDIR /var/www/package

# Instalando o Composer
RUN php -r "copy('http://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN rm /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite
