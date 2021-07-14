FROM php:7.2-apache
ARG XDEBUG_VERSION=2.6.0

RUN apt update && apt install -y libpq-dev libpng-dev libxml2-dev git
RUN docker-php-ext-install gd xml zip mbstring

RUN pecl install xdebug-2.6.0 && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/php.ini \

RUN pecl install xdebug-3.0.0 && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.var_display_max_data=512" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.var_display_max_depth=10" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.var_display_max_children=128" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.cli_color=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.show_local_vars=0" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.dump_globals=true" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.dump_once=true" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.dump_undefined=false;" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.max_stack_frames=-1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.show_error_trace=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.show_exception_trace=0" >> /usr/local/etc/php/php.ini \
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
