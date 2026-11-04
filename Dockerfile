FROM php:8.2-apache

# 1. Pasang dependensi sistem & driver PostgreSQL untuk PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Aktifkan modul mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# 3. Arahkan Document Root Apache ke folder /public milik Laravel (Penting demi keamanan!)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Tentukan folder kerja kontainer
WORKDIR /var/www/html

# 5. Salin semua file proyek SiSehat ke kontainer
COPY . .

# 6. Pasang Composer versi terbaru & instal dependensi PHP tanpa dev dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 7. Berikan hak akses (permissions) folder storage agar Laravel bisa menulis log/session
RUN chown -R www-data:www-data storage bootstrap/cache

# 8. Ekspos port 80 untuk lalu lintas web
EXPOSE 80

# 9. Jalankan skrip deploy.sh (migrasi & impor data) lalu nyalakan Apache Web Server
CMD sh deploy.sh && apache2-foreground
