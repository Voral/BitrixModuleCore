<?php
/** @noinspection PhpUnused */

/**
 * Базовый модуль для решений
 * @author Воробьев Александр
 * @version 1.0.0
 * @package vasoft.core
 * @see https://va-soft.ru/
 * @subpackage Установка модуля
 */

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SystemException;
use Vasoft\Core\Exceptions\DependencyExistsException;
use Vasoft\Core\System\Emitter;

Loc::loadMessages(__FILE__);

class vasoft_core extends CModule
{
    var $MODULE_ID = 'vasoft.core';
    public const MIN_PHP_VERSION = '8.1.0';
    public const MIN_CORE_VERSION = '23.500.200';

    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');
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
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection AccessModifierPresentedInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    function DoInstall()
    {
        global $APPLICATION;
        try {
            if ($this->isWrongCoreVersion()) {
                throw new SystemException(Loc::getMessage('ERROR_BITRIX_CORE', ['#VERSION#' => self::MIN_CORE_VERSION]));
            }
            if ($this->isWrongPHP()) {
                throw new SystemException(Loc::getMessage('ERROR_PHP_VERSION', ['#VERSION#', self::MIN_PHP_VERSION]));
            }
            ModuleManager::registerModule($this->MODULE_ID);
            $result = true;
        } catch (Exception $exception) {
            $result = false;
            $APPLICATION->ThrowException($exception->getMessage());
        }
        return $result;
    }

    /** @noinspection PhpMissingReturnTypeInspection
     * @noinspection AccessModifierPresentedInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @return bool
     * @throws ArgumentNullException
     * @throws LoaderException
     */
    function DoUninstall()
    {
        global $APPLICATION;
        Loader::includeModule($this->MODULE_ID);
        try {
            Emitter::emitRemove();
        } catch (DependencyExistsException $e) {
            $APPLICATION->throwException($e->getMessage() . '<ul><li>' . implode('</li><li>', $e->dependency) . '</li></ul>');
            return false;
        }
        Option::delete($this->MODULE_ID);
        ModuleManager::unRegisterModule($this->MODULE_ID);
        return true;
    }

    private function isWrongCoreVersion(): bool
    {
        return version_compare(ModuleManager::getVersion('main'), self::MIN_CORE_VERSION) < 0;
    }

    private function isWrongPHP(): bool
    {
        return version_compare(PHP_VERSION, self::MIN_PHP_VERSION) < 0;
    }
}
