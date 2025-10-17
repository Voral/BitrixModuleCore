<?php

declare(strict_types=1);
use Bitrix\Main\Loader;
use Vasoft\Core\Handlers\HandlerUpdater;

/**
 * @var CUpdater $updater
 */
if (IsModuleInstalled('vasoft.core')) {
    $updater->CopyFiles('install/components', 'components');
    Loader::includeModule('vasoft.core');
    (new HandlerUpdater())->check();
}
