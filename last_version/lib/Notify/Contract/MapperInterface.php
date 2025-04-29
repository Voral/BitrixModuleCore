<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Contract;

/**
 * Интерфейс преобразования результатов выполнения задач в сообщения для отправки уведомлений.
 */
interface MapperInterface
{
    /**
     * @param JobProcessorInterface[] $data Коллекция задач
     *
     * @return string[] Строки для отправки
     */
    public function map(array $data): array;
}
