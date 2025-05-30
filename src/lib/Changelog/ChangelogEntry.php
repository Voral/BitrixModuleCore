<?php

declare(strict_types=1);

namespace Vasoft\Core\Changelog;

class ChangelogEntry
{
    /**
     * @param string                  $version  Версия
     * @param \DateTimeImmutable      $date     Дата выпуска версии
     * @param array<ChangelogSection> $sections Разделы изменений
     * @param bool                    $last     Текущая версия
     */
    public function __construct(
        public readonly string $version,
        public readonly \DateTimeImmutable $date,
        public array $sections = [],
        public readonly bool $last = false,
    ) {}
}
