FROM php:8.2-apache


# Instala PDO + cliente MySQL + extensões necessárias para CodeIgniter
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libicu-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql intl \
    && docker-php-ext-enable mysqli

RUN echo "ServerName webserver" >> /etc/apache2/apache2.conf

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Define diretório padrão
WORKDIR /var/www/html
