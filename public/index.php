<?php

use Xver\MiCartera\Frontend\Symfony\Kernel;

$env = getenv("APP_ENV");
if (false === $env || 'test' !== $env) {
    putenv("OTEL_PHP_AUTOLOAD_ENABLED=true");
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
