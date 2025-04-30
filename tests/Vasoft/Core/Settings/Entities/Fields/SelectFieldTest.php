<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings\Entities\Fields;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vasoft\Core\Settings\Entities\Fields\SelectField
 *
 * @internal
 */
final class SelectFieldTest extends TestCase
{
    public function testRenderInputEmpty(): void
    {
        $field = new SelectField('test', 'Select it', static fn() => 'test');
        self::assertSame('<select name="test" ></select>', $field->renderInput());
    }

    public function testRenderInput(): void
    {
        $field = new SelectField('test', 'Select it', static fn() => 'test2');
        $field->configureOptions([
            'test1' => 'Test 1',
            'test2' => 'Test 2',
        ]);
        self::assertSame(
            '<select name="test" ><option value="test1" >Test 1</option><option value="test2" selected>Test 2</option></select>',
            $field->renderInput(),
        );
    }

    public function testRenderInputUnknownValue(): void
    {
        $field = new SelectField('test', 'Select it', static fn() => 'test212');
        $field->configureOptions([
            'test1' => 'Test 1',
            'test2' => 'Test 2',
        ]);
        self::assertSame(
            '<select name="test" ><option value="test1" >Test 1</option><option value="test2" >Test 2</option></select>',
            $field->renderInput(),
        );
    }

    public function testRenderInputMultiple(): void
    {
        $field = new SelectField('test', 'Select it', static fn() => ['test1', 'test4']);
        $field->configureMultiple();
        $field->configureOptions([
            'test1' => 'Test 1',
            'test2' => 'Test 2',
            'test3' => 'Test 3',
            'test4' => 'Test 4',
        ]);
        self::assertSame(
            '<select name="test[]" multiple><option value="test1" selected>Test 1</option><option value="test2" >Test 2</option><option value="test3" >Test 3</option><option value="test4" selected>Test 4</option></select>',
            $field->renderInput(),
        );
    }
}
