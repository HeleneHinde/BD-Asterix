#--
#-- Service => php
#-- Version => latest
#-- Name    => alpine-php
#--
FROM alpine:latest AS php

# Argument pour la date de création
ARG CREATED_AT

# Ajouter des métadonnées à l'image
LABEL namespace=pokedex
LABEL service=php
LABEL created_at=$CREATED_AT

# Mettre à jour Alpine et installer les paquets nécessaires
RUN apk add --no-cache php-common php-cli php-mysqli php-curl

# Exemple de commande à exécuter (vous pouvez personnaliser cette partie)
CMD ["php", "-a"]