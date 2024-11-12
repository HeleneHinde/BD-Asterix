# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Installer des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

RUN apt-get update && apt-get install -y default-mysql-client

# Copier le fichier de configuration Apache
COPY docker/images/symfony.conf /etc/apache2/sites-available/symfony.conf

# Activer le site Symfony et désactiver le site par défaut
RUN a2ensite symfony.conf && a2dissite 000-default.conf

# Activer les modules Apache nécessaires
RUN a2enmod rewrite

# Installer Composer (gestionnaire de dépendances PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers Symfony dans le conteneur
COPY . /var/www/html

# Donner les permissions au dossier
RUN chown -R www-data:www-data /var/www/html

# Exposer le port par défaut d'Apache
EXPOSE 80

# Lancer le serveur Apache
CMD ["apache2-foreground"]
