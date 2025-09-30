# Dockerfile
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
    zip \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    intl \
    opcache \
    mbstring \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier les fichiers de configuration Composer en premier
COPY composer.json composer.lock* ./

# Définir l'environnement AVANT l'installation des dépendances
ENV APP_ENV=dev
ENV APP_DEBUG=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances PHP avec les bonnes options
RUN composer install \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copier tout le projet
COPY . .

# Copier les fichiers d'environnement avec les bonnes permissions
COPY .env* ./

# Générer l'autoloader et exécuter les scripts post-install
RUN composer dump-autoload --optimize \
    && composer run-script post-install-cmd --no-interaction || true

# Créer les répertoires nécessaires avec les bonnes permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data /var/www/symfony \
    && chmod -R 755 /var/www/symfony \
    && chmod -R 777 var/

# Exposer le port PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]