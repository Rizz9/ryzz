FROM php:8.2-apache

# Copy semua file ke dalam container
COPY . /var/www/html/

# Aktifkan mod_rewrite jika perlu
RUN docker-php-ext-install mysqli && a2enmod rewrite

# Port 80 (default HTTP)
EXPOSE 80
