<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Sender;

use Vasoft\Core\Notify\Contract\SendServiceInterface;

/**
 * Консольный сервис отправки уведомлений.
 */
class Console implements SendServiceInterface
{
    public function __construct() {}

    /**
     * @param string[] $messageStrings
     */
    public function send(array $messageStrings): bool
    {
        echo $this->render($messageStrings);

        return true;
    }

    /**
     * @param string[] $messageStrings
     */
    private function render(array $messageStrings): string
    {
        $message = implode("\r\n", $messageStrings);
        $message = preg_replace('#<br\s*/?>#i', "\r\n", $message);

        return strip_tags($message);
    }
}
