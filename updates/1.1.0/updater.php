<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Vasoft\Core\Updater\HandlerDto;
use Vasoft\Core\Updater\HandlerInstaller;
use Vasoft\Core\Notify\Handlers\Main;
use Vasoft\Core\Changelog\Main as ChangelogMain;

/**
 * @var CUpdater $updater
 */
if ($updater->CanUpdateKernel()) {
    $filesForDelete = [
        0 => '/modules/vasoft.core/lib/Settings/Example/ExampleModuleSettings.php',
        1 => '/modules/vasoft.core/lib/Settings/Example/ExampleOptionNormalizer.php',
        2 => '/modules/vasoft.core/lib/Settings/Example/ExampleOptions.php',
        3 => '/modules/vasoft.core/lib/Updater/Example/DependencyHandler.php',
        4 => '/modules/vasoft.core/lib/Updater/Example/ExampleTable.php',
        5 => '/modules/vasoft.core/lib/Updater/Example/admin/example.php',
        6 => '/modules/vasoft.core/lib/Updater/Example/cli-example.php',
    ];
    foreach ($filesForDelete as $file) {
        CUpdateSystem::DeleteDirFilesEx($_SERVER['DOCUMENT_ROOT'] . $updater->kernelPath . '/' . $file);
    }
}
if (IsModuleInstalled('vasoft.core')) {
    $updater->CopyFiles('install/components', 'components');
    $adminFiles = [
        0 => 'changelog.php',
    ];
    foreach ($adminFiles as $file) {
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . $updater->kernelPath . '/admin/vasoft_core_' . $file,
            '<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/vasoft.core/install/admin/' . $file . '");',
        );
    }
    if (Loader::includeModule('vasoft.core')) {
        $handlerUpdater = new HandlerInstaller(
            'vasoft.core',
            [
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
                new HandlerDto(
                    'main',
                    'OnBuildGlobalMenu',
                    ChangelogMain::class,
                    'onBuildGlobalMenu',
                ),
            ],
        );
        $handlerUpdater->check();
    }
}
