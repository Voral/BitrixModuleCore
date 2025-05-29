<?php

declare(strict_types=1);

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('VS_CHANGELOG_NAME'),
    'DESCRIPTION' => Loc::getMessage('VS_CHANGELOG_DESCRIPTION'),
    'ICON' => '',
    'SORT' => 20,
    'CACHE_PATH' => 'Y',
    'PATH' => [
        'ID' => 'utility',
    ],
    'COMPLEX' => 'N',
];
