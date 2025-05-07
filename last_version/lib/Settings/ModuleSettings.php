<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

abstract class ModuleSettings
{
    /** @var callable[] */
    protected array $normalizer = [];
    private static array $instance = [];
    protected array $options = [];
    private array $registeredProperties = [];

    private bool $langLoaded = false;

    /** @var \ReflectionClass[] */
    private static array $reflections = [];

    /**
     * @param string $moduleCode Символьный код модуля
     * @param bool   $sendThrow  Выбрасывать исключения при запросе параметров, для которых не заданы значения
     *
     * @throws ArgumentNullException
     */
    protected function __construct(
        public readonly string $moduleCode,
        protected readonly bool $sendThrow = true,
        public readonly string $siteId = '',
    ) {
        $this->reload();
    }

    final protected function __clone()
    {
        throw new \LogicException('Cannot clone a singleton.');
    }

    /**
     * @throws \Exception
     */
    final public function __wakeup(): void
    {
        throw new \LogicException('Cannot unserialize a singleton.');
    }

    /**
     * @param string $moduleCode Символьный код модуля
     * @param bool   $sendThrow  Выбрасывать исключения при запросе параметров, для которых не заданы значения
     * @param string $siteId     Идентификатор сайта
     *
     * @throws ArgumentNullException
     */
    protected static function initInstance(string $moduleCode, bool $sendThrow = true, string $siteId = ''): static
    {
        $index = $moduleCode . $siteId;
        if (!array_key_exists($index, self::$instance)) {
            // @phpstan-ignore-next-line
            self::$instance[$index] = new static($moduleCode, $sendThrow, $siteId);
        }
        $instance = self::$instance[$index];
        assert($instance instanceof static);

        return $instance;
    }

    abstract public static function getInstance(bool $sendThrow = true): static;

    /**
     * Загрузка значений из базы.
     *
     * @throws ArgumentNullException
     */
    public function reload(): void
    {
        $this->options = Option::getForModule($this->moduleCode, $this->siteId);
    }

    /**
     * Установка значения для параметра.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @throws ArgumentOutOfRangeException
     */
    public function set($key, $value): void
    {
        $value = $this->normalize($key, $value);
        $this->options[$key] = $value;
        Option::set($this->moduleCode, $key, $value, $this->siteId);
    }

    /**
     * Возвращает
     */
    protected function getNormalizers(): array
    {
        if (empty($this->normalizer)) {
            $this->initNormalizers();
        }

        return $this->normalizer;
    }

    /**
     * Заполнение массива нормализатров.
     * Ключ - символьный код параметра
     * Значение - callable принимающий один параметр
     */
    abstract protected function initNormalizers(): void;

    /**
     * Выполнение нормализации значения согласно настройкам
     */
    protected function normalize(string $key, mixed $value): string
    {
        $normalizers = $this->getNormalizers();
        if (array_key_exists($key, $normalizers)) {
            $value = ($normalizers[$key])($value);
        }

        return (string) $value;
    }

    protected function getProperties(): array
    {
        if (empty($this->registeredProperties)) {
            $reflection = $this->getReflection();
            $this->registeredProperties = array_filter(
                $reflection->getConstants(),
                static fn($key) => str_starts_with($key, 'PROP_'),
                ARRAY_FILTER_USE_KEY,
            );
        }

        return $this->registeredProperties;
    }

    private function getReflection(): \ReflectionClass
    {
        if (!isset(self::$reflections[static::class])) {
            self::$reflections[static::class] = new \ReflectionClass(static::class);
        }

        return self::$reflections[static::class];
    }

    /**
     * Сохранение параметров из массива.
     *
     * @param array $data Массив значений, ключ - символьный код параметра
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function saveFromArray(array $data): void
    {
        $props = array_flip($this->getProperties());
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $props)) {
                $this->set($key, $value);
            }
        }
        $this->reload();
    }

    /**
     * Получение наименование параметра.
     */
    public function getOptionName(string $code): string
    {
        if (!$this->langLoaded) {
            $reflection = $this->getReflection();
            Loc::loadMessages($reflection->getFileName());
            $this->langLoaded = true;
        }

        return trim((string) Loc::getMessage($code));
    }

    public function clean(): void
    {
        Option::delete($this->moduleCode, ['site_id' => $this->siteId]);
        $this->options = [];
    }
}
