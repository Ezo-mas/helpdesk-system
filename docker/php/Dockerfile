FROM php:8.1-fpm

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy the application files into the container
COPY ../public /var/www/html
COPY ../src /var/www/html/src
COPY ../composer.json /var/www/html
COPY ../.env.example /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]