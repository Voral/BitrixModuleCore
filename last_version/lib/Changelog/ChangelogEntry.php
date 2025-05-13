<?php

declare(strict_types=1);

namespace Vasoft\Core\Changelog;

class ChangelogEntry
{
    /**
     * @param string                  $version Версия
     * @param \DateTimeImmutable      $date    Дата выпуска версии
     * @param array<ChangelogSection> $changes Разделы изменений
     */
    public function __construct(
        public readonly string $version,
        public readonly \DateTimeImmutable $date,
        public array $changes = [],
    ) {}
}
