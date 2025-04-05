<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // Priorité aux variables d'environnement système en CI/conteneur
    if (Kernel::isInCiOrContainer()) {
        // S'assurer que les variables d'environnement sont correctement prises en compte
        // sans chercher à lire les fichiers .env
        $context['APP_ENV'] = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';
        $context['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: '0';
    }
    
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
