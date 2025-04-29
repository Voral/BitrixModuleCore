<?php

declare(strict_types=1);

function register_custom_autoloader(): void
{
    $target = __DIR__ . '/../bx1/';
    $configFile = __DIR__ . '/../.vs-mock-builder.php';
    if (file_exists($configFile)) {
        $config = require $configFile;
        $target = $config['targetPath'] ?? $target;
    }
    spl_autoload_register(function ($class) use ($target): void {
        if (!str_contains($class, '\\')) {
            $filePath = $target . '/' . $class . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }
    });
}

register_custom_autoloader();