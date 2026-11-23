FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite expires headers deflate

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache para Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache-comverza.ini

COPY docker/php/apache-optimization.conf /etc/apache2/conf-available/comverza-optimization.conf

RUN a2enconf comverza-optimization

# Límites de subida de archivos
RUN printf "upload_max_filesize=10M\npost_max_size=12M\nmemory_limit=512M\n" > /usr/local/etc/php/conf.d/uploads.ini

COPY docker/entrypoint.sh /usr/local/bin/comverza-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/comverza-entrypoint && chmod +x /usr/local/bin/comverza-entrypoint

ENTRYPOINT ["comverza-entrypoint"]
