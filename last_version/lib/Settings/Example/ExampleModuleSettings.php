<?php /** @noinspection PhpUnused */

namespace Vasoft\Core\Settings\Example;

use Bitrix\Main\ArgumentNullException;
use Vasoft\Core\Settings\Exceptions\RequiredOptionException;
use Vasoft\Core\Settings\ModuleSettings;
use Vasoft\Core\Settings\Normalizers\Normalizer;

/**
 * Пример файла конфигурации
 */
class ExampleModuleSettings extends ModuleSettings
{
    public const MODULE_ID = 'vasoft.core.example';
    public const PROP_EXAMPLE_INT = 'EXAMPLE_INT';
    public const PROP_EXAMPLE_STRING = 'EXAMPLE_STRING';
    public const PROP_EXAMPLE_TEXT = 'EXAMPLE_TEXT';
    public const PROP_EXAMPLE_SELECT = 'EXAMPLE_SELECT';
    public const PROP_EXAMPLE_ARRAY = 'EXAMPLE_ARRAY';
    public const PROP_EXAMPLE_BOOL = 'EXAMPLE_BOOL';
    public const PROP_EXAMPLE_USER_GROUP = 'EXAMPLE_USER_GROUP';

    protected function initNormalizers(): void
    {
        $this->normalizer = [
            self::PROP_EXAMPLE_STRING => [Normalizer::class, 'normalizeString'],
            self::PROP_EXAMPLE_ARRAY => [Normalizer::class, 'normalizeString'],
            self::PROP_EXAMPLE_BOOL => [Normalizer::class, 'normalizeBoolean'],
            self::PROP_EXAMPLE_SELECT => [ExampleOptionNormalizer::class, 'normalize'],
            self::PROP_EXAMPLE_INT => [Normalizer::class, 'normalizeNotZeroInt'],
        ];
    }

    /**
     * @param bool $sendThrow
     * @return static
     * @throws ArgumentNullException
     */
    public static function getInstance(bool $sendThrow = true): static
    {
        return self::initInstance(self::MODULE_ID, $sendThrow);
    }

    public function getExampleUserGroupId(): int
    {
        return $this->options[self::PROP_EXAMPLE_USER_GROUP] ?? 0;
    }

    public function getExampleBoolean(): string
    {
        return $this->options[self::PROP_EXAMPLE_BOOL] ?? 'N';
    }

    public function isExampleBoolean(): bool
    {
        return $this->getExampleBoolean() === 'Y';
    }

    public function getExampleArray(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_ARRAY] ?? '');
    }

    public function getExampleArrayList(): array
    {
        return explode("\n", $this->getExampleArray());
    }

    /**
     * Геттер параметра у которого должно быть значение. Отключение исключений необходимо для страницы в админке,
     * когда есть возможность, что значения еще не задали
     * @return string | int
     * @throws RequiredOptionException
     */
    public function getExampleInt(): string|int
    {
        if (!array_key_exists(self::PROP_EXAMPLE_INT, $this->options)) {
            if ($this->sendThrow) {
                throw new RequiredOptionException(self::PROP_EXAMPLE_INT, $this->getOptionName(self::PROP_EXAMPLE_INT));
            }
            return 0;
        }
        return (int)$this->options[self::PROP_EXAMPLE_INT];
    }

    public function getExampleText(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_TEXT] ?? '');
    }

    public function getExampleSelect(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_SELECT] ?? '');
    }

    public function getExampleString(): string
    {
        return trim($this->options[self::PROP_EXAMPLE_STRING] ?? 'default value');
    }
}