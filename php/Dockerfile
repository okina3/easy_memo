FROM php:8.1-apache

EXPOSE 5173

RUN apt update \
        && apt install -y --no-install-recommends \
            g++ \  
            libicu-dev \
            libpq-dev \
            libzip-dev \
            libpng-dev \    
            libjpeg-dev \   
            zip \
            zip unzip \
            zlib1g-dev \
            npm \
            nodejs \
            vim \
        && docker-php-ext-configure gd --with-jpeg \ 
        && docker-php-ext-install \
            intl \
            opcache \
            pdo \
            pdo_pgsql \
            pgsql \
            pdo_mysql \
            -j$(nproc) gd   

WORKDIR /var/www/sample

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer