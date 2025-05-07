<?php

declare(strict_types=1);

use Vasoft\MockBuilderBitrix\Configurator;

$bitrixModulesPaths = '';
$local = __DIR__ . '/.vs-mock-builder.local.php';
if (file_exists($local)) {
    $bitrixModulesPaths = require_once $local;
}

$configurator = new Configurator($bitrixModulesPaths);
$config = $configurator->getBitrixMockBuilderSettings(__DIR__ . '/bx/', ['main'], targetPhpVersion: '8.1');
$config['resultTypes'] = [
    'Bitrix\Main\Application::getConnection' => '\Bitrix\Main\DB\Connection',
    'Bitrix\Main\ORM\Query::fetchCollection' => '\Bitrix\Main\ORM\Objectify\Collection',
];

return $config;
