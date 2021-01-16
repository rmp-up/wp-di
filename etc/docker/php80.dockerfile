FROM pretzlaw/php:8.0-apache

RUN docker-php-ext-enable mysqli zip
RUN pecl install xdebug && \
    echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20200930/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini
