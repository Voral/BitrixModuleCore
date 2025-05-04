<?php

declare(strict_types=1);

use Vasoft\MockBuilder\Visitor\AddMockToolsVisitor;
use Vasoft\MockBuilder\Visitor\PublicAndConstFilter;
use Vasoft\MockBuilder\Visitor\SetReturnTypes;

$basePaths = '';
$local = __DIR__ . '/.vs-mock-builder.local.php';
if (file_exists($local)) {
    $basePaths = require_once $local;
}

return [
    'basePath' => $basePaths,
    'targetPath' => __DIR__ . '/bx/',
    'resultTypes' => [
        'Bitrix\Main\Config\Configuration::get' => 'mixed',
        'Bitrix\Main\ORM\Query\Query::fetchCollection' => '\Bitrix\Main\ORM\Objectify\Collection',
        'Bitrix\Main\EventResult::getParameters' => 'mixed',
        'Bitrix\Main\EventResult::getModuleId' => 'string',
        'Bitrix\Main\EventResult::getType' => 'int',
        'Bitrix\Main\ORM\Data\DataManager::query' => '\Bitrix\Main\ORM\Query\Query',
        'Bitrix\Main\ORM\Entity::getDBTableName' => 'string',
        'Bitrix\Main\ORM\Fields\Relations\Reference::validateValue' => '\Bitrix\Main\ORM\Fields\Result',
    ],
    'visitors' => [
        new PublicAndConstFilter(true),
        new SetReturnTypes('8.1', true),
        new AddMockToolsVisitor('Bitrix', true),
    ],
];
