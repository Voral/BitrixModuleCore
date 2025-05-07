<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Sender;

use Vasoft\Core\Notify\Contract\SendServiceInterface;

/**
 * Сервис отправки сообщений в телеграмм
 */
class Telegram implements SendServiceInterface
{
    public const URL_TEMPLATE = 'https://api.telegram.org/bot%s/';
    public const URL_SEND_MESSAGE = 'sendMessage';

    /**
     * @param string $token  Token пользователя
     * @param string $chatId Идентификатор чата
     */
    public function __construct(
        private readonly string $token,
        private readonly string $chatId,
    ) {}

    /**
     * @param string[] $messageStrings
     *
     * @return array<mixed>
     */
    public function send(array $messageStrings): array
    {
        if (empty($this->token) || empty($this->chatId)) {
            return [];
        }

        return $this->push($this->render($messageStrings));
    }

    /**
     * @return array<mixed>
     */
    private function push(string $message): array
    {
        $url = sprintf(self::URL_TEMPLATE, $this->token) . self::URL_SEND_MESSAGE;
        $data = [
            'chat_id' => $this->chatId,
            'text' => $message,
        ];
        $content = file_get_contents(
            $url,
            false,
            stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => json_encode($data),
                ],
            ]),
        );

        return $content ? json_decode($content, true) : [];
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
