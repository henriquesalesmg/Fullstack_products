
# Multistage: build frontend separado
FROM php:8.2-fpm
WORKDIR /var/www
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    postgresql-client \
    nodejs \
    npm
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www
RUN npm install --legacy-peer-deps && npm run build
RUN composer install --optimize-autoloader --no-dev
CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000
