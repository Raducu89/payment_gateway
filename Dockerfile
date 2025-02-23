FROM php:8.3-fpm

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libonig-dev libzip-dev unzip curl git libpng-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www

# Copy the application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Access rights for storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
