<?php

declare(strict_types=1);

namespace Vasoft\Core\Tests\Notify\Sender;

use PHPUnit\Framework\TestCase;
use Vasoft\Core\Notify\Sender\Console;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Core\Notify\Sender\Console
 */
final class ConsoleTest extends TestCase
{
    public function testSender(): void
    {
        $expectedOutput = "Plain text message\r\nTest HTML message\r\nMultiline HTML message \r\n line 2\r\n line 3";

        $sender = new Console();
        ob_start();
        $sender->send([
            'Plain text message',
            'Test <b>HTML</b> message',
            'Multiline <b>HTML</b> message <br> line 2<BR /> line 3',
        ]);
        $consoleOutput = ob_get_clean();
        self::assertSame($expectedOutput, $consoleOutput);
    }
}
