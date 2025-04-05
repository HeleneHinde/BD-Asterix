<?php

namespace App;

use Symfony\Component\Runtime\SymfonyRuntime;

class CustomRuntime extends SymfonyRuntime
{
    /**
     * Override la méthode getenv pour éviter de charger les fichiers .env en CI/conteneur
     */
    protected function getEnv(array $context): array
    {
        if (Kernel::isInCiOrContainer()) {
            // En CI/conteneur, on utilise directement les variables d'environnement système
            // sans essayer de charger les fichiers .env
            return array_merge(
                $_ENV,
                $_SERVER,
                [
                    'APP_ENV' => $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod',
                    'APP_DEBUG' => $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: '0',
                    'APP_SECRET' => $_SERVER['APP_SECRET'] ?? $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: bin2hex(random_bytes(16)),
                    'MESSENGER_TRANSPORT_DSN' => $_SERVER['MESSENGER_TRANSPORT_DSN'] ?? $_ENV['MESSENGER_TRANSPORT_DSN'] ?? getenv('MESSENGER_TRANSPORT_DSN') ?: 'doctrine://default',
                ]
            );
        }

        // En local, comportement normal avec chargement des fichiers .env
        return parent::getEnv($context);
    }
}