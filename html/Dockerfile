FROM php:8.2-apache

# Installeer MySQLi extensie voor PHP
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Kopieer de projectbestanden naar de Apache webroot
COPY . /var/www/html/

# Zorg voor correcte bestandsrechten
RUN chown -R www-data:www-data /var/www/html

# Stel poort 80 open voor HTTP
EXPOSE 80
