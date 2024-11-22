# PHP 8.1とApacheをベースイメージとして使用
FROM php:8.1-apache

# Apache用のポートを公開
EXPOSE 5173

# パッケージを更新し、依存関係をインストール
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

# Composerをグローバルにインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Xdebugをインストールして設定
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# PHPのカスタム設定ファイルをコピー
COPY php.ini /usr/local/etc/php/php.ini

# ワークディレクトリを/var/www/easy_memoに設定
WORKDIR /var/www/easy_memo