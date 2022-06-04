# Set the base image for subsequent instructions
FROM php:7.4

WORKDIR /var/www

# Update packages

RUN apt-get update \
    && apt-get install -y libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev git \
    && apt-get clean

RUN apt-get install zip unzip
RUN pecl install xdebug-2.9.1
RUN pecl install redis

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql pcntl opcache gd
RUN docker-php-ext-enable pdo pdo_mysql pcntl opcache xdebug redis
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.default_enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.max_nesting_level=-1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .
#COPY .env.example .env

CMD ["bash", "./laravue-entrypoint.sh"]
