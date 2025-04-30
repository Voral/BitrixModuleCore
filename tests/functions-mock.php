<?php

declare(strict_types=1);
use Bitrix\Mocker\FunctionMocker;

function BeginNote(): string
{
    return FunctionMocker::executeMocked('BeginNote', []);
}

function EndNote(): string
{
    return FunctionMocker::executeMocked('EndNote', []);
}

function GetMessage($name, $aReplace = null): string
{
    return FunctionMocker::executeMocked('GetMessage', [$name, $aReplace]);
}
