FROM php:7.2-apache

# Disable composer root warning
ENV COMPOSER_ALLOW_SUPERUSER=1

# Update packages and install dependencies
RUN apt-get update \
 && apt-get install -y git zlib1g-dev libicu-dev g++ curl gnupg git unzip

# Setup Apache and PHP
RUN a2enmod rewrite ssl \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && docker-php-ext-install intl bcmath pdo_mysql > /dev/null \
 && service apache2 restart

# Setup Composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# Set work dir
WORKDIR /var/www