<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Permet de détecter si nous sommes en environnement CI ou conteneur
     */
    public static function isInCiOrContainer(): bool
    {
        // Détecter les environnements CI courants
        if (getenv('CI') || getenv('GITHUB_ACTIONS') || getenv('GITLAB_CI') || getenv('JENKINS_URL')) {
            return true;
        }

        // Détecter les environnements conteneurisés
        if (file_exists('/.dockerenv') || getenv('KUBERNETES_SERVICE_HOST')) {
            return true;
        }

        // Variable personnalisée qui peut être définie dans vos environnements CI ou conteneurs
        if (getenv('APP_NO_DOT_ENV')) {
            return true;
        }

        return false;
    }
}
