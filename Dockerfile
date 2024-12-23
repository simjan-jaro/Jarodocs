# 1. Velg et grunnleggende Docker-image for PHP med Apache (webserver)
FROM php:8.1-apache

# 2. Kopier alle prosjektfiler fra mappen din til Docker-containeren
#    Dette plasserer filene dine i standardkatalogen for Apache: /var/www/html
COPY . /var/www/html/

# 3. Gi webserveren (Apache) nødvendige rettigheter til å lese prosjektfilene
RUN chown -R www-data:www-data /var/www/html

# 4. Installer og aktiver PHP-utvidelsen for MySQL (siden du bruker databasen)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 5. Eksponer standard webserverport (80) slik at andre kan få tilgang til appen
EXPOSE 80
