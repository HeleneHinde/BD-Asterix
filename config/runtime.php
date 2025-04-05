<?php

use App\CustomRuntime;
use App\Kernel;

// Si nous sommes en CI/conteneur, utiliser notre runtime personnalisé
if (Kernel::isInCiOrContainer()) {
    return new CustomRuntime();
}

// Sinon, utiliser le runtime Symfony standard
$bootstrap = dirname(__DIR__).'/vendor/symfony/runtime/Runtime.php';
if (!file_exists($bootstrap)) {
    $bootstrap = dirname(__DIR__, 3).'/runtime/Runtime.php';
}
require_once $bootstrap;

return new \Symfony\Component\Runtime\Runtime();