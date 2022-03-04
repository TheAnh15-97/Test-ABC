FROM php:8.0-apache

RUN apt-get update

RUN apt-get install -y \
 git \
 zip \
 curl \
 sudo \
 unzip \
 libicu-dev \
 libbz2-dev \
 libpng-dev \
 libjpeg-dev \
 libmcrypt-dev \
 libreadline-dev \
 libfreetype6-dev \
 libzip-dev \
 g++

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
 bz2 \
 intl \
 iconv \
 bcmath \
 opcache \
 calendar \
 pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV NODE_VERSION=16.13.0
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version

ARG uid
RUN useradd -G www-data,root -u $uid -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
 chown -R devuser:devuser /home/devuser