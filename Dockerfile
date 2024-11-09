# Utiliser une image officielle de PHP avec FPM
FROM php:8.1-fpm

# Installer apt-utils séparément pour voir les erreurs plus facilement
RUN apt-get update && \
    apt-get install -y --no-install-recommends apt-utils && \
    rm -rf /var/lib/apt/lists/*

# Installer une première série de dépendances
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les bibliothèques GD et les dépendances d'image
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Configurer et installer l'extension GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd

# Installer les extensions PDO, PDO_PGSQL et ZIP
RUN docker-php-ext-install -j$(nproc) pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le répertoire de travail
WORKDIR /var/www

# Copier les fichiers de configuration avant les fichiers du projet
COPY nginx/default.conf /etc/nginx/sites-available/default
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh && \
    rm -f /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copier les fichiers du projet
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copier le reste des fichiers du projet
COPY . .

# Configurer les permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Exposer le port
EXPOSE 8089

# Lancer le script de démarrage
CMD ["/usr/local/bin/start.sh"]
