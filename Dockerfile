FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-pgsql \
    php8.1-pdo \
    libapache2-mod-php8.1 \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Limpiar directorio web completamente
RUN rm -rf /var/www/html/*

# Copiar archivos del proyecto
COPY . /var/www/html/

# Verificar que index.php existe
RUN ls -la /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configurar Apache para usar index.php
RUN echo 'DirectoryIndex index.php index.html' > /etc/apache2/mods-enabled/dir.conf

RUN echo '<Directory /var/www/html>\n\
    Options Indexes
