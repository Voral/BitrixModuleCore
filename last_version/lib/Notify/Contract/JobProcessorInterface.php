<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Contract;

/**
 *  Интерфейс обработчика задачи.
 */
interface JobProcessorInterface
{
    /**
     * Выполнить задачу.
     */
    public function execute(): void;

    /**
     * Вернуть массив строк результатов выполнения для отправки.
     *
     * @return string[]
     */
    public function getMessageStrings(): array;
}
