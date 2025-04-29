<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Contract;

/**
 * Интерфейс сервиса для отправки уведомлений.
 */
interface SendService
{
    /**
     * Отправить уведомление.
     *
     * @param string[] $messageStrings массив сообщение для отправки
     */
    public function send(array $messageStrings): mixed;
}
