FROM php:8.2-apache

# Instalar extensiones PHP necesarias (PDO, cURL para webhooks/Ollama)
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# Copiar configuración PHP personalizada
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Crear carpeta uploads con permisos
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Crear carpeta logs
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs