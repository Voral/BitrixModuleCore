<?php

declare(strict_types=1);

namespace Vasoft\Core\Changelog;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

class Main
{
    /**
     * @param array<mixed> $aGlobalMenu
     * @param array<mixed> $aModuleMenu
     */
    public static function onBuildGlobalMenu(array &$aGlobalMenu, array &$aModuleMenu): void
    {
        global $APPLICATION;
        $file = Loader::getDocumentRoot() . '/local/CHANGELOG.md';
        if (file_exists($file) && $APPLICATION->GetGroupRight('vasoft.core') >= 'R') {
            $arSubItems = [
                [
                    'parent_menu' => 'vasoft_core',
                    'sort' => 10,
                    'text' => Loc::getMessage('VS_CORE_MENU_CHANGELOG'),
                    'title' => Loc::getMessage('VS_CORE_MENU_CHANGELOG'),
                    'url' => 'vasoft_core_changelog.php?lang=ru',
                    'items_id' => 'vasoft_core_changelog',
                ],
            ];
            $aGlobalMenu['vasoft_core'] = [
                'menu_id' => 'vasoft_core',
                'text' => Loc::getMessage('VS_CORE_MENU_PROJECT'),
                'title' => Loc::getMessage('VS_CORE_MENU_PROJECT'),
                'url' => 'vasoft_core_changelog.php?lang=ru',
                'sort' => 1000,
                'items_id' => 'vasoft_core_',
                'help_section' => 'custom',
                'more_url' => [
                    'vasoft_core_changelog.php?lang=ru',
                ],
                'items' => $arSubItems,
            ];
        }
    }
}
