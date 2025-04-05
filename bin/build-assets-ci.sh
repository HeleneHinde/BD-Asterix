#!/bin/bash

# Script pour construire les assets sans dépendre du fichier .env
# À utiliser en environnement CI

# Générer un fichier .env temporaire avec les variables minimales requises
echo "APP_ENV=${APP_ENV:-prod}" > .env.build
echo "APP_SECRET=${APP_SECRET:-$(openssl rand -hex 16)}" >> .env.build

# Variables pour Messenger (utiliser les valeurs par défaut si non définies)
echo "MESSENGER_TRANSPORT_DSN=${MESSENGER_TRANSPORT_DSN:-doctrine://default}" >> .env.build

# Exécuter le build avec ce fichier temporaire
APP_ENV=prod APP_DEBUG=0 symfony console cache:clear --env=prod --no-warmup --env-file=.env.build
yarn install
yarn build

# Supprimer le fichier temporaire
rm .env.build

echo "Build des assets terminé avec succès!"