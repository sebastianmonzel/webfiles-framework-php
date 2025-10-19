FROM php:7.4-apache


LABEL maintainer="mail@sebastianmonzel.de"

# Install required packages and PHP extensions
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install gd \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

EXPOSE 80

