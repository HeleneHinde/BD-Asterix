# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Installer des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    curl \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Installer Yarn via npm
RUN npm install --global yarn

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

# Télécharger et installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN su www-data -s /bin/bash -c "composer install"

# Créer le fichier SQLite
RUN mkdir -p /var/www/html/var/data && touch /var/www/html/var/data/bd.sqlite

# Assurer les bonnes permissions pour le fichier SQLite
RUN chown -R www-data:www-data /var/www/html/var/data
