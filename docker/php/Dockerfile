# Imagen base
FROM php:8.2-fpm

# Actualizar+Instalar librerias/dependencias
RUN apt-get update && apt-get install -y \ 
    # Libreria para control de versiones
    git \     
    # Libreria para descompresión de archivos
    unzip \
    # Libreria para trabajar con ZIP
    libzip-dev \
    # Libreria para trabajar con Unicode
    libicu-dev

# Instalar extensiones PHP
# pdo para trabajar con bases de datos #pdo_mysql para trabajar con MySQL 
# zip para trabajar con archivos ZIP # intl para trabajar con Unicode
RUN docker-php-ext-install pdo pdo_mysql zip intl

# Instalar Composer, un gestor de librerias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Carpeta de trabajo
WORKDIR /var/www/html