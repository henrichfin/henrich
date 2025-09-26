# Use official PHP Apache image
FROM php:8.2-apache

# Install MySQL extension for PHP
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite for .htaccess
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Create a simple PHP info page for health checks
RUN echo "<?php phpinfo(); ?>" > /var/www/html/info.php

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
