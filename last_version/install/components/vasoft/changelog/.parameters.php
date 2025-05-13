<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'PARSER_GROUP' => [
            'NAME' => Loc::getMessage('VS_CHANGELOG_PARSER_GROUP'),
            'SORT' => 1000,
        ],
    ],
    'PARAMETERS' => [
        'FILE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('VS_CHANGELOG_PROP_FILE'),
            'TYPE' => 'STRING',
            'VALUE' => '',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'COLS' => 50,
        ],
        'VERSION_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('VS_CHANGELOG_PROP_VERSION_COUNT'),
            'TYPE' => 'STRING',
            'VALUE' => '',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'COLS' => 10,
        ],
        'FILTER' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('VS_CHANGELOG_PROP_FILTER'),
            'TYPE' => 'STRING',
            'VALUE' => '',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'COLS' => 10,
        ],
        'VERSION_REGEXP' => [
            'PARENT' => 'PARSER_GROUP',
            'NAME' => Loc::getMessage('VS_CHANGELOG_PROP_REGEXP_VERSION'),
            'TYPE' => 'STRING',
            'VALUE' => '',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'COLS' => 150,
        ],
        'SECTION_REGEXP' => [
            'PARENT' => 'PARSER_GROUP',
            'NAME' => Loc::getMessage('VS_CHANGELOG_PROP_REGEXP_SECTION'),
            'TYPE' => 'STRING',
            'VALUE' => '',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'COLS' => 150,
        ],
    ],
];
