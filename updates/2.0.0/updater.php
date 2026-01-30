<?php

declare(strict_types=1);

/**
* @var CUpdater $updater
*/

if(IsModuleInstalled('vasoft.core')){
    \Bitrix\Main\Loader::includeModule('vasoft.core');
(new \Vasoft\Core\Handlers\HandlerUpdater())->check();
}
