FROM php:8.1-apache

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite headers

# Configurar timezone
RUN echo "date.timezone=America/Lima" > /usr/local/etc/php/conf.d/timezone.ini

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
