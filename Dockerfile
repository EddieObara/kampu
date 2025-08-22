# Use official PHP + Apache image
FROM php:8.2-apache

# Enable Apache modules (optional but often useful)
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Copy all project files into Apache's web directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 (Render uses this by default)
EXPOSE 80