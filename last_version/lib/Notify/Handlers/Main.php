<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vasoft\Core\Notify\Handlers;

use Bitrix\Main\Config\Configuration;
use Vasoft\Core\Notify\Sender\Telegram;

class Main
{
    public static string $senderClass = Telegram::class;

    public static function getTelegramSender(): ?Telegram
    {
        $options = Configuration::getInstance()->get('vasoftCore');
        $token = $options['notifier']['telegram']['token'] ?? '';
        $channelId = $options['notifier']['telegram']['channels']['backup'] ?? '';
        if ('' === $token || '' === $channelId) {
            return null;
        }
        $sender = new (static::$senderClass)($token, $channelId);

        return $sender instanceof Telegram ? $sender : null;
    }

    public static function onAutoBackupUnknownError(mixed $payload): void
    {
        $sender = self::getTelegramSender();
        $payload['START_TIME'] = isset($payload['START_TIME']) ? date(
            'Y-m-d H:i:s',
            $payload['START_TIME'],
        ) : 'Unknown';
        $sender?->send([
            'Run backup at ' . $payload['START_TIME'],
            'Error: ' . ($payload['ERROR'] ?? 'Unknown'),
        ]);
    }

    public static function onAutoBackupSuccess(mixed $payload): void
    {
        $sender = self::getTelegramSender();
        $payload['START_TIME'] = isset($payload['START_TIME']) ? date(
            'Y-m-d H:i:s',
            $payload['START_TIME'],
        ) : 'Unknown';
        $size = isset($payload['arc_size']) ? sprintf('%0.2f', $payload['arc_size'] / 1024 / 1024) : 'Unknown';
        $sender?->send(['Run backup at ' . $payload['START_TIME'], 'Size ' . $size]);
    }
}
