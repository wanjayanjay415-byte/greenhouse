FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
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

RUN a2enmod rewrite headers

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's!/var/www!${APACHE_DOCUMENT_ROOT}/..!g' /etc/apache2/apache2.conf

RUN echo '<Directory ${APACHE_DOCUMENT_ROOT}>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

RUN sed -i 's/Listen 80//' /etc/apache2/ports.conf

RUN echo '#!/bin/bash\n\
PORT=${PORT:-8080}\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN echo "upload_max_filesize = 10M" >> "$PHP_INI_DIR/php.ini" \
    && echo "post_max_size = 12M" >> "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable \
    && chmod -R 775 /var/www/html/public/uploads

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
