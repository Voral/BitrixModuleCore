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
echo "Registering custom autoloader: {$target}\n";
    spl_autoload_register(function ($class) use ($target): void {
        echo "Search {$class}\n";
        if (!str_contains($class, '\\')) {
            $filePath = $target . '/' . $class . '.php';
            echo "Try {$filePath}\n";
            if (file_exists($filePath)) {
                echo "Found!\n";
                require_once $filePath;
            }
        }
    });
}

register_custom_autoloader();