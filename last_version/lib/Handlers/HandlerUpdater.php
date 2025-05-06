<?php

declare(strict_types=1);

namespace Vasoft\Core\Handlers;

use Vasoft\Core\Notify\Handlers\Main;
use Vasoft\Core\Settings\ModuleConfig;
use Vasoft\Core\Updater\HandlerDto;
use Vasoft\Core\Updater\HandlerInstaller;

class HandlerUpdater extends HandlerInstaller
{
    public function __construct()
    {
        parent::__construct(ModuleConfig::MODULE_ID, [
            new HandlerDto(
                'main',
                'OnAutoBackupUnknownError',
                Main::class,
                'onAutoBackupUnknownError',
            ),
            new HandlerDto(
                'main',
                'OnAutoBackupSuccess',
                Main::class,
                'onAutoBackupSuccess',
            ),
        ]);
    }
}
