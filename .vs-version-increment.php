<?php

declare(strict_types=1);

use Vasoft\VersionIncrement\Config;
use Vasoft\VersionIncrement\Events\EventType;
use Voral\BitrixModuleTool\ModuleListener;

$config = new Config();

$eventBus = $config->getEventBus();
$eventBus->addListener(EventType::BEFORE_VERSION_SET, new ModuleListener($config));
$config->setSection('fix', 'Исправления')
    ->setSection('feat', 'Новый функционал')
    ->setSection('build', 'Build system', hidden: true)
    ->setSection('test', 'Tests', hidden: true)
    ->setSection('docs', 'Documentation', hidden: true)
    ->setSection('chore', 'Other changes', hidden: true)
    ->setHideDoubles(true)
    ->setEnabledComposerVersioning(false);

return $config;
