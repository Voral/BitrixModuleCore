<?php

declare(strict_types=1);

namespace Vasoft\Core\Exceptions;

use Bitrix\Main\Localization\Loc;

class DependencyExistsException extends ModuleException
{
    public function __construct(public readonly array $dependency)
    {
        parent::__construct(Loc::getMessage('ERROR_DEPENDENCY_EXISTS'));
    }
}
