<?php

declare(strict_types=1);
use Bitrix\Mocker\MockFunctions;

function BeginNote(): string
{
    return MockFunctions::executeMocked('BeginNote', []);
}

function EndNote(): string
{
    return MockFunctions::executeMocked('EndNote', []);
}

function GetMessage($name, $aReplace = null): string
{
    return MockFunctions::executeMocked('GetMessage', [$name, $aReplace]);
}
