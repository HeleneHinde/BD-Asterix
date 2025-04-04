#!/bin/bash

mkdir -p var/data

# Exécuter les commandes Symfony
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migration:migrate --no-interaction
git config --global --add safe.directory /var/www/html
git config --global --add safe.directory '*'
yarn install
yarn build

# Continuer avec la commande par défaut
exec "$@"
