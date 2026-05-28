<?php

use Symfony\Component\Dotenv\Dotenv;
use Xver\MiCartera\Frontend\Symfony\Kernel;

$env = getenv("APP_ENV");
if (false === $env || 'test' !== $env) {
    putenv("OTEL_PHP_AUTOLOAD_ENABLED=true");
}

if (class_exists(Dotenv::class)) {
    $dotenv = new Dotenv();
    $dotenv->load(dirname(__DIR__).'/versions.env');
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
