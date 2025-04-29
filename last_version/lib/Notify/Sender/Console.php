<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Sender;

use Vasoft\Core\Notify\Contract\SendService;

/**
 * Консольный сервис отправки уведомлений.
 */
class Console implements SendService
{
    public function __construct() {}

    public function send(array $messageStrings): bool
    {
        echo $this->render($messageStrings);

        return true;
    }

    private function render($messageStrings): string
    {
        $message = implode("\r\n", $messageStrings);
        $message = preg_replace('#<br\s* /?>#i', "\r\n", $message);

        return strip_tags($message);
    }
}
