<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

/*
 * Базовый модуль для решений
 * @author Воробьев Александр
 * @package vasoft.core
 * @see https://va-soft.ru/
 * @subpackage Установка модуля
 */

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\IO\FileNotFoundException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SystemException;
use Vasoft\Core\Exceptions\DependencyExistsException;
use Vasoft\Core\Handlers\HandlerUpdater;
use Vasoft\Core\System\Emitter;
use Bitrix\Main\ArgumentException;
use Vasoft\Core\Updater\FileInstaller;

Loc::loadMessages(__FILE__);

class vasoft_core extends CModule
{
    public $MODULE_ID = 'vasoft.core';
    public const MIN_PHP_VERSION = '8.1.0';
    public const MIN_CORE_VERSION = '23.500.200';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_ID = 'vasoft.core';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('VASOFT_CORE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('VASOFT_CORE_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'VASoft';
        $this->PARTNER_URI = 'https://va-soft.ru/';

        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    /**
     * @noinspection AccessModifierPresentedInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function DoInstall(): bool
    {
        global $APPLICATION;
        $result = false;

        try {
            if ($this->isWrongCoreVersion()) {
                throw new SystemException(
                    Loc::getMessage('ERROR_BITRIX_CORE', ['#VERSION#' => self::MIN_CORE_VERSION]),
                );
            }
            if ($this->isWrongPHP()) {
                throw new SystemException(Loc::getMessage('ERROR_PHP_VERSION', ['#VERSION#', self::MIN_PHP_VERSION]));
            }
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallEvents();
            $result = true;
        } catch (Exception $exception) {
            $APPLICATION->ThrowException($exception->getMessage());
        }

        return $result;
    }

    /**
     * @noinspection AccessModifierPresentedInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     *
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws FileNotFoundException
     * @throws LoaderException
     * @throws SqlQueryException
     */
    public function DoUninstall(): bool
    {
        global $APPLICATION;
        Loader::includeModule($this->MODULE_ID);
        $result = false;

        try {
            Emitter::emitRemove();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            Option::delete($this->MODULE_ID);
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $result = true;
        } catch (DependencyExistsException $e) {
            $APPLICATION->throwException(
                $e->getMessage() . '<ul><li>' . implode('</li><li>', $e->dependency) . '</li></ul>',
            );
        }

        return $result;
    }

    private function isWrongCoreVersion(): bool
    {
        return version_compare(ModuleManager::getVersion('main'), self::MIN_CORE_VERSION) < 0;
    }

    private function isWrongPHP(): bool
    {
        return version_compare(PHP_VERSION, self::MIN_PHP_VERSION) < 0;
    }

    /**
     * @throws SqlQueryException
     */
    public function InstallEvents(): void
    {
        (new HandlerUpdater())->check();
    }

    /**
     * @throws SqlQueryException
     */
    public function UnInstallEvents(): void
    {
        (new HandlerUpdater())->clean();
    }

    /**
     * @throws FileNotFoundException
     */
    public function InstallFiles(): void
    {
        (new FileInstaller('vasoft_core_', __DIR__))->checkAdminPages();
        CopyDirFiles(__DIR__ . '/components/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components');
    }

    /**
     * @throws FileNotFoundException
     */
    public function UnInstallFiles(): void
    {
        (new FileInstaller('vasoft_core_', __DIR__))->cleanAdminPages();
        DeleteDirFiles(__DIR__ . '/components/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components');
    }
}
