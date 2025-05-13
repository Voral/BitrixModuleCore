<?php

declare(strict_types=1);

namespace Vasoft\Core\Changelog;

class ChangelogSection
{
    /**
     * @param string        $title Название раздела
     * @param array<string> $items Список изменений
     */
    public function __construct(
        public string $title,
        public array $items = [],
    ) {}
}
