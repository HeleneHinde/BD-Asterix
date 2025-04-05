#!/bin/bash

mkdir -p var/data

# Activer le mode "pas de .env" pour signaler que nous utilisons uniquement des variables d'environnement
export APP_NO_DOT_ENV=1

# Vérifier si le build des assets est nécessaire
if [[ "${SKIP_ASSETS_BUILD:-false}" == "false" ]]; then
    echo "Construction des assets avec les variables d'environnement..."
    yarn install
    yarn build
else
    echo "Construction des assets ignorée (SKIP_ASSETS_BUILD=true)"
fi

# Vider le cache
php bin/console cache:clear --no-warmup

# Exécuter les migrations de base de données en utilisant les variables d'environnement
php bin/console doctrine:database:create --if-not-exists --no-interaction
php bin/console doctrine:migration:migrate --no-interaction

git config --global --add safe.directory /var/www/html
git config --global --add safe.directory '*'

# Continuer avec la commande par défaut
exec "$@"
