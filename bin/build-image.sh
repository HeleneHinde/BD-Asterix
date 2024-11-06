#!/bin/bash

NGINX_VERSION=1.23
PHP_VERSION=8.3
NAMESPACE=pokedex
CREATED_AT=$(date +%F-%H-%M-%S)

cd docker/images/base

# PHP
DOCKER_BUILDKIT=0 docker build --pull --build-arg CREATED_AT=$CREATED_AT . --target=php -t hub.docker.com/_/alpine/latest
