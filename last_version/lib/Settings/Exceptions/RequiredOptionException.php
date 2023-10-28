<?php

namespace Vasoft\Core\Settings\Exceptions;

use Bitrix\Main\Localization\Loc;
use Vasoft\Core\Exceptions\ModuleException;

class RequiredOptionException extends ModuleException
{
    public function __construct(
        string $code,
        string $name
    )
    {
        parent::__construct(sprintf(Loc::getMessage('REQUIRED_OPTION_EXCEPTION'), $name, $code));
    }
}