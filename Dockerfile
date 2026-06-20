FROM php:8.2-apache

# Force cache invalidation - change this value to force rebuild
ARG CACHEBUST=20250620v3
RUN echo "Building with CACHEBUST=${CACHEBUST}"

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        xml \
        intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure Apache: only mpm_prefork, enable rewrite
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite headers

# Configure PHP
RUN if [ -f "$PHP_INI_DIR/php.ini-production" ]; then \
        cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    elif [ -f "$PHP_INI_DIR/php.ini-development" ]; then \
        cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
    fi \
    && echo "upload_max_filesize = 10M" >> "$PHP_INI_DIR/php.ini" \
    && echo "post_max_size = 12M" >> "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini"

# Copy application code
COPY . /var/www/html/
WORKDIR /var/www/html

# Install Composer + dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Configure Apache document root → public/
RUN sed -ri 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Add custom Apache config for public directory
COPY docker-apache.conf /etc/apache2/conf-available/greenhouse.conf
RUN a2enconf greenhouse

# Entrypoint handles dynamic PORT from Railway
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
