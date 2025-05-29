<?php

declare(strict_types=1);
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Vasoft\Core\Settings\Render;

use Vasoft\Core\Settings\Entities\Tab;
use Bitrix\Main\Localization\Loc;

class Controller
{
    /**
     * @param Tab[] $tabs
     */
    public function __construct(
        public readonly array $tabs,
        public readonly bool $needRightsTab,
    ) {}

    public function startTabControl(string $tabControlName): \CAdminTabControl
    {
        $tabs = [];
        foreach ($this->tabs as $tab) {
            $tabs[] = $tab->map();
        }
        if ($this->needRightsTab) {
            $tabs[] = [
                'DIV' => 'rights',
                'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
                'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'),
            ];
        }
        $tabControl = new \CAdminTabControl($tabControlName, $tabs);
        $tabControl->Begin();

        return $tabControl;
    }

    public function echoTabs(\CAdminTabControl $tabControl): void
    {
        foreach ($this->tabs as $tab) {
            // @noinspection DisconnectedForeachInstructionInspection
            $tabControl->BeginNextTab();
            foreach ($tab->fields as $field) {
                echo $field->render();
            }
        }
        if ($this->needRightsTab) {
            $tabControl->BeginNextTab();
        }
    }
}
