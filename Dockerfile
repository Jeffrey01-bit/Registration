FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libsasl2-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-mongodb --ignore-platform-req=ext-redis

# Copy application code
COPY . .

# Create uploads directory with proper permissions
RUN mkdir -p uploads && chmod 777 uploads

# Simple start command
CMD php -S 0.0.0.0:$PORT -t .