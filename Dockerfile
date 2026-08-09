FROM php:8.3.30-fpm

# Instala herramientas necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev gnupg ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl sockets bcmath gd

#instala Redis fijando a una versión comprobada
RUN pecl install redis-6.3.0 && docker-php-ext-enable redis

# Instala Node.js 22.0
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && node -v && npm -v

# Instala Composer globalmente (fijado a 2.9.5)
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY custom.ini /usr/local/etc/php/conf.d/custom.ini
