FROM php:7.2-fpm-alpine

RUN apk update \
    && apk add  --no-cache bash git sqlite curl libmcrypt libmcrypt-dev openssh-client icu-dev \
    libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev g++ make autoconf \
    && docker-php-source extract \
    && docker-php-source delete \
    && docker-php-ext-install gd pdo_mysql intl zip \
    && rm -rf /tmp/*

ENV APP_ENV='dev' \
    APP_DEBUG=1 

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

#Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo 'alias composer_d="php -d memory_limit=-1 /usr/local/bin/composer"' >> ~/.bashrc && source ~/.bashrc

WORKDIR /var/www/html

COPY ./composer.* /var/www/html/
RUN composer install --no-scripts

COPY ./ /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN composer install


CMD ["php-fpm", "-F"]

EXPOSE 9000
