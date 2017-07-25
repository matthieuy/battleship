FROM php:7.0-fpm
MAINTAINER Matthieu YK <matthieuy@riseup.net>

# Apt-get
RUN apt-get update && apt-get install -y --no-install-recommends \
        apt-transport-https \
        ca-certificates \
        curl \
        git \
        gnupg2 \
        libcurl4-gnutls-dev \
        libicu-dev \
        libpng-dev \
        libsodium-dev \
        libxml2-dev \
        libzmq3-dev \
        lsb-release \
        sudo \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# PHP extension
RUN docker-php-ext-install pdo pdo_mysql curl gd intl xml

# PHP-ZMQ
RUN git clone --branch master https://github.com/mkoppanen/php-zmq --depth=1 /tmp/php-zmq \
        && cd /tmp/php-zmq \
        && phpize  \
        && ./configure  \
        && make  \
        && make install \
        && make test \
        && rm -rf /tmp/php-zmq
COPY zmq.ini /usr/local/etc/php/conf.d/

# Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer \
    && rm /tmp/composer-setup.php \
    && composer --version

# NodeJS
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - && apt-get install -y nodejs && rm -rf /var/lib/apt/lists/*
RUN npm install -g yarn bower

# Xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Others
RUN usermod -u 1000 www-data
RUN echo '#!/bin/bash\nsudo -u www-data /usr/local/bin/php /var/www/battleship/bin/console $@' >> /usr/local/bin/sf && chmod +x /usr/local/bin/sf

WORKDIR /var/www/battleship
EXPOSE 8080
