#!/bin/bash

mkdir -p var/data

# Vérifier si le build est nécessaire
if [[ "${SKIP_ASSETS_BUILD:-false}" == "false" ]]; then
    # Vérifier si un fichier .env existe
    if [ ! -f .env ]; then
        echo "Aucun fichier .env trouvé. Création d'un fichier temporaire pour le build..."
        
        # Création d'un fichier .env temporaire avec les variables minimales requises
        echo "APP_ENV=${APP_ENV:-dev}" > .env.temp
        echo "APP_SECRET=${APP_SECRET:-$(openssl rand -hex 16)}" >> .env.temp
        echo "MESSENGER_TRANSPORT_DSN=${MESSENGER_TRANSPORT_DSN:-doctrine://default}" >> .env.temp
        
        # Utiliser ce fichier temporaire pour la construction
        APP_ENV=prod php bin/console cache:clear --no-warmup --env-file=.env.temp
        yarn install
        yarn build
        
        # Supprimer le fichier temporaire
        rm .env.temp
    else
        # Utiliser le fichier .env existant
        yarn install
        yarn build
    fi
else
    echo "Construction des assets ignorée (SKIP_ASSETS_BUILD=true)"
fi

# Exécuter les commandes Symfony (avec gestion de l'absence de .env)
if [ -f .env ] || [ ! -z "${DATABASE_URL}" ]; then
    php bin/console doctrine:database:create --if-not-exists
    php bin/console doctrine:migration:migrate --no-interaction
else
    echo "ATTENTION: Impossible d'exécuter les migrations car aucun fichier .env ou variable DATABASE_URL n'est disponible"
fi

git config --global --add safe.directory /var/www/html
git config --global --add safe.directory '*'

# Continuer avec la commande par défaut
exec "$@"
