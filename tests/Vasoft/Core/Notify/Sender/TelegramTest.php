<?php

declare(strict_types=1);

namespace Vasoft\Core\Notify\Sender;

use PHPUnit\Framework\TestCase;

include __DIR__ . '/MockTrait.php';

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Notify\Sender\Telegram
 */
final class TelegramTest extends TestCase
{
    use MockTrait;

    public function testSendEmptyToken(): void
    {
        $this->initMocks();
        $this->clearMockFileGetContents([]);
        $this->clearMockStreamContextCreate([]);
        $sender = new Telegram('', '123');
        $sender->send(['hello']);
        self::assertSame(0, self::$mockFileGetContentsCount);
        self::assertSame(0, self::$mockStreamContextCreateCount);
    }

    public function testSendEmptyChatId(): void
    {
        $this->initMocks();
        $this->clearMockFileGetContents([]);
        $this->clearMockStreamContextCreate([]);
        $sender = new Telegram('12', '');
        $sender->send(['hello']);
        self::assertSame(0, self::$mockFileGetContentsCount);
        self::assertSame(0, self::$mockStreamContextCreateCount);
    }

    public function testSend(): void
    {
        $this->initMocks();
        $this->clearMockFileGetContents([
            'https://api.telegram.org/bot12/sendMessage' => '{}',
        ]);
        $this->clearMockStreamContextCreate([]);
        $data = [
            'chat_id' => '123',
            'text' => "Plain text message\r\nTest HTML message\r\nMultiline HTML message \r\n line 2\r\n line 3",
        ];

        $sender = new Telegram('12', $data['chat_id']);
        $sender->send([
            'Plain text message',
            'Test <b>HTML</b> message',
            'Multiline <b>HTML</b> message <br> line 2<BR /> line 3',
        ]);
        self::assertSame(1, self::$mockFileGetContentsCount);
        self::assertSame(1, self::$mockStreamContextCreateCount);
        self::assertSame(
            [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => json_encode($data),
                ],
            ],
            self::$mockStreamContextCreateParam[0],
        );
    }
}
