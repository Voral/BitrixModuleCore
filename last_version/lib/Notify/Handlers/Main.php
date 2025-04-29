<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace Vasoft\Core\Notify\Handlers;

use Bitrix\Main\Config\Configuration;
use Vasoft\Core\Notify\Sender\Telegram;

class Main
{
    public static function onAutoBackupUnknownError(mixed $payload): void
    {
        $options = Configuration::getInstance()->get('vasoftCore');
        $token = $options['notifier']['telegram']['token'] ?? '';
        $channelId = $options['notifier']['telegram']['channels']['backup'] ?? '';
        if ('' === $token || '' === $channelId) {
            return;
        }
        $sender = new Telegram($token, $channelId);

        $sender->send([
            'Run backup at ' . date('Y-m-d H:i:s', $payload['START_TIME']),
            'Error: ' . $payload['ERROR'],
        ]);
    }

    public static function onAutoBackupSuccess(mixed $payload): void
    {
        $options = Configuration::getInstance()->get('vasoftCore');
        $token = $options['notifier']['telegram']['token'] ?? '';
        $channelId = $options['notifier']['telegram']['channels']['backup'] ?? '';
        if ('' === $token || '' === $channelId) {
            return;
        }
        $sender = new Telegram($token, $channelId);

        $sender->send([
            'Run backup at ' . date('Y-m-d H:i:s', $payload['START_TIME']),
            sprintf('Size %0.2f', $payload['arc_size'] / 1024 / 1024),
        ]);
    }
}
