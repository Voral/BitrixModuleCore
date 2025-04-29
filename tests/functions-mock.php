<?php

declare(strict_types=1);

function BeginNote(): string
{
    return '<note>';
}

function EndNote(): string
{
    return '</note>';
}

function GetMessage($name, $aReplace = null): string
{
    return 'Message';
}
