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

RUN rm -rf /var/www/html/*

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

COPY apache-visor.conf /etc/apache2/sites-available/000-default.conf

RUN a2ensite 000-default

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
