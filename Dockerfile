FROM php:8.4-rc-fpm
USER root
ENV ACCEPT_EULA=Y

# Install required dependencies
RUN set -eux; \
    apt-get update && \
    apt-get install -y --no-install-recommends \
    ca-certificates curl gnupg nano locales apt-transport-https \
    libpq-dev libfreetype6-dev libjpeg62-turbo-dev zlib1g-dev libzip-dev \
    libtidy-dev libonig-dev libicu-dev libaio1 g++ wget rsync git zip unzip \
    libpng-dev libxrender1 libfontconfig1 fontconfig libfontconfig1-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) gd intl pdo_mysql pdo_pgsql pgsql zip exif pcntl bcmath mbstring opcache sockets && \
    docker-php-source delete && \
    rm -rf /var/lib/apt/lists/*

# Set up command aliases
RUN echo 'alias art="php artisan"' | tee -a ~/.bashrc && \
    echo 'alias artm="php artisan migrate"' | tee -a ~/.bashrc && \
    echo 'alias artrl="php artisan route:list"' | tee -a ~/.bashrc && \
    echo 'alias ccc="php artisan config:clear && php artisan cache:clear && php artisan config:cache"' | tee -a ~/.bashrc

# Set locale
RUN locale-gen ru_RU.UTF-8
ENV LANG=ru_RU.UTF-8 \
    LANGUAGE=ru_RU.UTF-8 \
    LC_ALL=ru_RU.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY --chown=www-data:www-data . .

# Set permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Switch to Laravel user
USER www-data

# Expose port and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
