<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify;

use Vasoft\Core\Notify\Contract\SendService;
use Vasoft\Core\Notify\Job\JobMapper;

/**
 * Выполнение и отправка результатов работы очереди задач.
 */
class Notifier
{
    /**
     * @param array       $jobs   Очередь задач
     * @param JobMapper   $mapper Отображение задач
     * @param SendService $sender Сервис отправки сообщений
     */
    public function __construct(
        private readonly array $jobs,
        private readonly JobMapper $mapper,
        private readonly SendService $sender,
    ) {}

    public function process(): void
    {
        foreach ($this->jobs as $job) {
            $job->execute();
        }
        $messages = $this->mapper->map($this->jobs);
        $this->sender->send($messages);
    }
}
