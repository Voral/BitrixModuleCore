<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Normalizers;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Normalizers\Normalizer
 *
 * @internal
 */
final class NormalizerTest extends TestCase
{
    public function testNormalizeInt(): void
    {
        self::assertSame('1', Normalizer::normalizeInt('1'));
        self::assertSame('0', Normalizer::normalizeInt(''));
        self::assertSame('0', Normalizer::normalizeInt(0));
        self::assertSame('0', Normalizer::normalizeInt('das1'));
        self::assertSame('1', Normalizer::normalizeInt('1das0'));
    }

    public function testNormalizeNotZeroInt(): void
    {
        self::assertSame('1', Normalizer::normalizeNotZeroInt('1'));
        self::assertSame('', Normalizer::normalizeNotZeroInt(''));
        self::assertSame('', Normalizer::normalizeNotZeroInt(0));
        self::assertSame('', Normalizer::normalizeNotZeroInt('das1'));
        self::assertSame('1', Normalizer::normalizeNotZeroInt('1das0'));
    }

    public function testNormalizeString(): void
    {
        self::assertSame('test', Normalizer::normalizeString('test'));
        self::assertSame('test', Normalizer::normalizeString(' test '));
        self::assertSame('test', Normalizer::normalizeString(" test \n"));
    }

    public function testNormalizeCommaSeparatedString(): void
    {
        self::assertSame('test', Normalizer::normalizeCommaSeparatedString('test'));
        self::assertSame('test,test1', Normalizer::normalizeCommaSeparatedString('test,  test1'));
        self::assertSame('test,test1', Normalizer::normalizeCommaSeparatedString('test ,  test1'));
        self::assertSame('test,test1', Normalizer::normalizeCommaSeparatedString('test,test1,'));
        self::assertSame('test,test1', Normalizer::normalizeCommaSeparatedString(",test,test1,\n"));
    }

    public function testNormalizeBoolean(): void
    {
        self::assertSame('Y', Normalizer::normalizeBoolean('Y'));
        self::assertSame('Y', Normalizer::normalizeBoolean('y'));
        self::assertSame('Y', Normalizer::normalizeBoolean(' y '));
        self::assertSame('N', Normalizer::normalizeBoolean('n'));
        self::assertSame('N', Normalizer::normalizeBoolean('z'));
        self::assertSame('N', Normalizer::normalizeBoolean(''));
    }

    public function testNormalizeCommaSeparatedInteger(): void
    {
        self::assertSame('1', Normalizer::normalizeCommaSeparatedInteger('1'));
        self::assertSame('1,123,31', Normalizer::normalizeCommaSeparatedInteger('1,123,31  '));
        self::assertSame('1,123,31', Normalizer::normalizeCommaSeparatedInteger(', 1, 123, 31  ,'));
        self::assertSame('', Normalizer::normalizeCommaSeparatedInteger('das1'));
        self::assertSame('', Normalizer::normalizeCommaSeparatedInteger(''));
        self::assertSame('', Normalizer::normalizeCommaSeparatedInteger('1das0'));
        self::assertSame('', Normalizer::normalizeCommaSeparatedInteger('1das0,2das0'));
        self::assertSame('', Normalizer::normalizeCommaSeparatedInteger('1das0, 2das0,'));
    }
}
