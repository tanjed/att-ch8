FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    cron

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js (needed for npm and Vite compilation)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install built-in PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# We don't copy the code here because we will map it via volume in docker-compose
# for local development. However, we setup proper user permissions.

# Create www user matching the host user ID (typically 1000 for Linux Desktop)
RUN useradd -G www-data,root -u 1000 -d /home/admin admin
RUN mkdir -p /home/admin/.composer && \
    chown -R admin:admin /home/admin

USER admin

# Start PHP-FPM
CMD ["php-fpm"]
