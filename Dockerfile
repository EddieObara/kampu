# Use the official PHP + Apache image
FROM php:8.2-apache

# Enable Apache modules if needed (rewrite is common)
RUN a2enmod rewrite

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 (Render will automatically use this)
EXPOSE 80
