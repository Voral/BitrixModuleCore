<?php

declare(strict_types=1);

use Vasoft\MockBuilderBitrix\Autoloader;

$path = realpath(__DIR__ . '/vendor/voral/mock-builder-bitrix/src/') . '/Autoloader.php';

if (file_exists($path)) {
    include_once $path;
    (new Autoloader(__DIR__ . '/bx'))->registerAll();
} else {
    exit($path . ' not found');
}
include_once __DIR__ . '/tests/aliases.php';
