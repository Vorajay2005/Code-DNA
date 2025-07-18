FROM php:8.1-apache

# Enable mod_rewrite (common need for PHP apps)
RUN a2enmod rewrite

# Copy app files to web root
COPY . /var/www/html/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run composer install
RUN composer install

# Expose port 80
EXPOSE 80
