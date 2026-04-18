FROM php:8.2-apache
# =============================================================
# Immagine base condivisa per tutti i progetti didattici.
# Costruisci una volta sola con:
#   docker build -t didattica-php:latest docker-base-image/
# La configurazione Apache è inclusa qui dentro: i singoli
# progetti non montano più alcun volume apache-config.
# =============================================================

# Argomenti opzionali per il proxy (es. rete scolastica)
ARG PROXY_HOST
ARG PROXY_PORT

# Configura il proxy se fornito
RUN if [ -n "$PROXY_HOST" ] && [ -n "$PROXY_PORT" ]; then \
        echo "Acquire::http::Proxy \"http://$PROXY_HOST:$PROXY_PORT\";" > /etc/apt/apt.conf.d/proxy.conf && \
        echo "Acquire::https::Proxy \"http://$PROXY_HOST:$PROXY_PORT\";" >> /etc/apt/apt.conf.d/proxy.conf; \
    fi

# Installa le estensioni PHP e gli strumenti necessari
RUN apt-get update && apt-get install -y \
    vim \
    curl \
    zip \
    unzip \
    git \
    libzip-dev \
    libgd-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*

# Abilita mod_rewrite e mod_ssl
RUN a2enmod rewrite ssl

# Genera certificati SSL self-signed per lo sviluppo locale (validi 10 anni)
RUN openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
    -keyout /etc/ssl/private/ssl-cert-snakeoil.key \
    -out /etc/ssl/certs/ssl-cert-snakeoil.pem \
    -subj "/C=IT/ST=Italy/L=Brescia/O=Didattica/CN=localhost"

# Installa Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia la configurazione Apache (comune a tutti i progetti)
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache-config/ssl-default.conf /etc/apache2/sites-available/ssl-default.conf
RUN a2ensite ssl-default

# Copia configurazione PHP per lo sviluppo
COPY apache-php/php.ini /usr/local/etc/php/

# Permessi cartella web e storage
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

EXPOSE 80 443

CMD ["apache2-foreground"]
