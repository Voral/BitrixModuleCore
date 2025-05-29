<?php

declare(strict_types=1);

/**
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 * @global CUser $USER
 * @global CAdminPage $adminPage
 * @global CAdminSidePanelHelper $adminSidePanelHelper
 *
 * @var CAdminAjaxHelper $adminAjaxHelper
 */
global $adminPage;
global $adminSidePanelHelper;

global $listScriptName;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

$moduleId = 'm204.api';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
Loader::includeModule($moduleId);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/iblock/prolog.php';
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/interface/admin_lib.php');

if ($APPLICATION->GetGroupRight($moduleId) <= 'D' && $APPLICATION->GetGroupRight('vasoft.core') < 'R') {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$adminPage = new CAdminPage();
$selfFolderUrl = $adminPage->getSelfFolderUrl();


$elementsList = [];
if (isset($_REQUEST['mode']) && ('list' === $_REQUEST['mode'] || 'frame' === $_REQUEST['mode'])) {
    CFile::DisableJSFunction(true);
}


$APPLICATION->SetTitle(Loc::getMessage('VS_CORE_TITLE_CHANGELOG'));

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

if (!empty($_REQUEST['IFRAME']) && 'Y' === $_REQUEST['IFRAME']) {
    $APPLICATION->RestartBuffer();
}

$APPLICATION->IncludeComponent('vasoft:changelog', 'admin', [
    'FILE' => $_SERVER['DOCUMENT_ROOT'] . '/local/CHANGELOG.md',
]);
if (!empty($_REQUEST['IFRAME']) && 'Y' === $_REQUEST['IFRAME']) {
    exit;
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
