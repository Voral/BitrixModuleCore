<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Handlers;

use Bitrix\Main\Config\Configuration;
use Vasoft\MockBuilder\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Vasoft\Core\Notify\Sender\Telegram;

/**
 * @coversDefaultClass \Vasoft\Core\Notify\Handlers\Main
 *
 * @internal
 */
final class MainTest extends TestCase
{
    /**
     * @throws \ReflectionException
     *
     * @dataProvider provideGetTelegramSenderNoConfigCases
     */
    public function testGetTelegramSenderNoConfig(array $config): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        Configuration::cleanMockData('get', defaultDefinition: new MockDefinition(result: $config));
        self::assertNull(Main::getTelegramSender());
    }

    public static function provideGetTelegramSenderNoConfigCases(): iterable
    {
        return [
            [[]],
            [['notifier' => ['telegram' => ['token' => '']]]],
            [['notifier' => ['telegram' => ['token' => '123144']]]],
            [['notifier' => ['telegram' => ['channels' => ['backup' => 'test']]]]],
        ];
    }

    public function testGetTelegramSender(): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        Configuration::cleanMockData(
            'get',
            defaultDefinition: new MockDefinition(
                result: ['notifier' => ['telegram' => ['channels' => ['backup' => 'test'], 'token' => '123144']]],
            ),
        );
        Main::$senderClass = TestTelegramSender::class;
        $sender = Main::getTelegramSender();
        self::assertSame('123144', $sender->token1);
        self::assertSame('test', $sender->channel1);
    }

    public function testOnAutoBackupUnknownError(): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        $time = time();
        $payload = ['START_TIME' => $time, 'ERROR' => 'Test'];
        Configuration::cleanMockData(
            'get',
            defaultDefinition: new MockDefinition(
                result: ['notifier' => ['telegram' => ['channels' => ['backup' => 'test'], 'token' => '123144']]],
            ),
        );
        Main::$senderClass = TestTelegramSender::class;
        Main::onAutoBackupUnknownError($payload);
        self::assertSame(
            ['Run backup at ' . date('Y-m-d H:i:s', $payload['START_TIME']), 'Error: Test'],
            TestTelegramSender::$messages,
        );
    }

    public function testOnAutoBackupUnknownErrorEmptyPayload(): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        Configuration::cleanMockData(
            'get',
            defaultDefinition: new MockDefinition(
                result: ['notifier' => ['telegram' => ['channels' => ['backup' => 'test'], 'token' => '123144']]],
            ),
        );
        Main::$senderClass = TestTelegramSender::class;
        Main::onAutoBackupUnknownError([]);
        self::assertSame(
            ['Run backup at Unknown', 'Error: Unknown'],
            TestTelegramSender::$messages,
        );
    }

    public function testOnAutoBackupSuccess(): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        Configuration::cleanMockData(
            'get',
            defaultDefinition: new MockDefinition(
                result: ['notifier' => ['telegram' => ['channels' => ['backup' => 'test'], 'token' => '123144']]],
            ),
        );
        Main::$senderClass = TestTelegramSender::class;
        $time = time();
        $payload = ['START_TIME' => $time, 'arc_size' => 7654604];
        Main::onAutoBackupSuccess($payload);
        self::assertSame(
            ['Run backup at ' . date('Y-m-d H:i:s', $payload['START_TIME']), 'Size 7.30'],
            TestTelegramSender::$messages,
        );
    }

    public function testOnAutoBackupSuccessEmptyPayload(): void
    {
        Configuration::cleanMockData('getInstance', defaultDefinition: new MockDefinition(result: new Configuration()));
        Configuration::cleanMockData(
            'get',
            defaultDefinition: new MockDefinition(
                result: ['notifier' => ['telegram' => ['channels' => ['backup' => 'test'], 'token' => '123144']]],
            ),
        );
        Main::$senderClass = TestTelegramSender::class;
        Main::onAutoBackupSuccess([]);
        self::assertSame(
            ['Run backup at Unknown', 'Size Unknown'],
            TestTelegramSender::$messages,
        );
    }
}

class TestTelegramSender extends Telegram
{
    public static array $messages = [];

    public function __construct(public readonly string $token1, public readonly string $channel1)
    {
        parent::__construct($token1, $channel1);
    }

    public function send(array $messageStrings): array
    {
        self::$messages = $messageStrings;

        return [];
    }
}
