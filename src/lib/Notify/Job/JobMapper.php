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
            static function (array $curry, JobProcessorInterface $job) {
                $messages = $job->getMessageStrings();
                if (!empty($curry)) {
                    $curry[] = '';
                }

                return empty($messages) ? $curry : array_merge($curry, $messages);
            },
            [],
        );
    }
}
