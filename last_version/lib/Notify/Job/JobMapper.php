<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Job;

use Vasoft\Core\Notify\Contract\JobProcessorInterface;
use Vasoft\Core\Notify\Contract\MapperInterface;

class JobMapper implements MapperInterface
{
    /**
     * @param JobProcessorInterface[] $data Коллекция задач
     *
     * @return string[] Строки для отправки
     */
    public function map(array $data): array
    {
        return array_reduce(
            $data,
            static fn(array $curry, JobProcessorInterface $job) => array_merge($curry, [''], $job->getMessageStrings()),
            [],
        );
    }
}
