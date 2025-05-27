# Dockerfile
FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy codebase
COPY . .

# Tandai direktori project sebagai safe untuk Git
RUN git config --global --add safe.directory /var/www/html

# Composer: install dependencies sesuai composer.lock
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
