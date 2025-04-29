<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Job;

use Vasoft\Core\Notify\Contract\JobProcessorInterface;

/**
 * Задача сбора информации о сервере.
 */
class SystemInfo implements JobProcessorInterface
{
    private array $result = [];

    public function execute(): void
    {
        $this->result = [
            'free_space' => disk_free_space('/'),
        ];
    }

    public function getMessageStrings(): array
    {
        return [
            sprintf('Free space: %0.2f', $this->result['free_space'] / 1024 / 1024 / 1024),
        ];
    }
}
