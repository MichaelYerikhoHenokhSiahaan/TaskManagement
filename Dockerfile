FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libpq-dev \
    libzip-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
    bcmath \
    curl \
    intl \
    mbstring \
    pdo \
    pdo_pgsql \
    xml \
    zip
RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

RUN composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist \
    && npm ci --no-audit --no-fund \
    && npm run build \
    && chown -R www-data:www-data storage bootstrap/cache
