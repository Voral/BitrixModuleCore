<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

use Bitrix\Main\ArgumentNullException;

class ModuleConfig extends ModuleSettings
{
    public const MODULE_ID = 'vasoft.core';

    /**
     * @return ModuleConfig
     *
     * @throws ArgumentNullException
     */
    public static function getInstance(bool $sendThrow = true): static
    {
        return self::initInstance(self::MODULE_ID, $sendThrow);
    }

    protected function initNormalizers(): void
    {
        $this->normalizer = [
        ];
    }
}
