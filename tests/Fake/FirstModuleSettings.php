<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Fake;

use Vasoft\Core\Settings\Exceptions\RequiredOptionException;
use Vasoft\Core\Settings\ModuleSettings;
use Vasoft\Core\Settings\Normalizers\Normalizer;

class FirstModuleSettings extends ModuleSettings
{
    public const MODULE_ID = 'tests.module1';
    public const PROP_EXAMPLE_INT = 'EXAMPLE_INT';
    public const PROP_EXAMPLE_STRING = 'EXAMPLE_STRING';

    public static function getInstance(bool $sendThrow = true, string $siteId = ''): static
    {
        return self::initInstance(self::MODULE_ID, $sendThrow, $siteId);
    }

    protected function initNormalizers(): void
    {
        $this->normalizer = [
            self::PROP_EXAMPLE_STRING => [Normalizer::class, 'normalizeString'],
            self::PROP_EXAMPLE_INT => [Normalizer::class, 'normalizeNotZeroInt'],
        ];
    }

    /**
     * Геттер параметра у которого должно быть значение. Отключение исключений необходимо для страницы в админке,
     * когда есть возможность, что значения еще не задали.
     *
     * @throws RequiredOptionException
     */
    public function getExampleInt(): int|string
    {
        if (!array_key_exists(self::PROP_EXAMPLE_INT, $this->options)) {
            if ($this->sendThrow) {
                throw new RequiredOptionException(self::PROP_EXAMPLE_INT, $this->getOptionName(self::PROP_EXAMPLE_INT));
            }

            return 0;
        }

        return (int) $this->options[self::PROP_EXAMPLE_INT];
    }

    public function getExampleString(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_STRING] ?? 'default value');
    }
}
