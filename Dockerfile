FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

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
    python3 \
    python3-pip \
    python3-venv \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Playwright and its dependencies
# We need to install python packages first to get the playwright cli
COPY python/requirements.txt /tmp/requirements.txt
RUN pip3 install -r /tmp/requirements.txt --break-system-packages

# Install Playwright browsers (as the user who will run them)
USER $user
RUN playwright install chromium
