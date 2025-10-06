# Étape 1 : Construction (Phase intermédiaire)
# Utilise l'image PHP officielle optimisée pour la construction
FROM composer:2.7 as vendor

# Copie le code de l'application
COPY . /app
WORKDIR /app

# Installe les dépendances Composer (sans les dépendances de développement)
# Le --ignore-platform-reqs est souvent nécessaire pour les conteneurs
RUN composer install --no-dev --ignore-platform-reqs --optimize-autoloader

# Étape 2 : Production (Image finale plus petite et sécurisée)
# Utilise une image alpine plus légère pour la production
FROM php:8.2-fpm-alpine

# Installe les extensions PHP nécessaires (ajoutez ici toutes celles utilisées : pdo_mysql, gd, etc.)
# Nous ajoutons pdo_pgsql pour la base de données PostgreSQL
RUN apk update && apk add \
    postgresql-dev \
    libpq \
    && docker-php-ext-install -j$(nproc) pdo_pgsql

# Configure le répertoire de travail
WORKDIR /var/www

# Copie UNIQUEMENT les fichiers nécessaires de l'étape de construction précédente (vendor et le code)
COPY --from=vendor /app /var/www

# Exécute l'optimisation de Laravel (cache de configuration)
RUN php artisan config:cache

# S'assure que PHP-FPM utilise l'utilisateur 'www-data' (meilleure sécurité)
USER www-data

# Expose le port par défaut de PHP-FPM
EXPOSE 9000

# Définit le point d'entrée pour démarrer l'application
# Nous allons utiliser un serveur web séparé (comme Nginx) dans un autre conteneur (Render gère ça pour nous)
# Ici, nous nous assurons que le FPM est prêt.
CMD ["php-fpm"]
