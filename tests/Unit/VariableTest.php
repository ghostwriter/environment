<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Variable;

/**
 * @coversDefaultClass \Ghostwriter\Environment\Variable
 *
 * @internal
 *
 * @small
 */
final class VariableTest extends AbstractTestCase
{
    private VariableInterface $variable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->variable = new Variable('NAME', 'V4lu3');
    }

    /**
     * @return iterable<string,array<int,string>>
     */
    public function environmentVariablesProvider(): iterable
    {
        yield from [
            'default' => ['name', 'value'],
            'empty-name' => ['', 'value'],
            'empty-value' => ['name', ''],
            'untrimmed-name' => [' name ', 'value'],
            'untrimmed-value' => ['name', ' value '],
            'name-contains-equal-sign' => ['na=me', 'value'],
            'value-contains-equal-sign' => ['name', 'val=ue'],
            'name-contains-NULL-byte' => ["na\0me", 'value'],
            'value-contains-NULL-byte' => ['name', "val\0ue"],
        ];
    }

    /**
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     * @dataProvider environmentVariablesProvider
     */
    public function testConstruct(string $name, string $value): void
    {
        $trimmedName = trim($name);
        if (
            '' === $trimmedName ||
            $name !== $trimmedName ||
            str_contains($name, '=') ||
            str_contains($name, "\0")
        ) {
            $this->expectException(InvalidNameException::class);
        }

        if (
            $value !== trim($value) ||
            str_contains($value, "\0")
        ) {
            $this->expectException(InvalidValueException::class);
        }

        self::assertSame($name, (new Variable($name, $value))->getName());
        self::assertSame($value, (new Variable($name, $value))->getValue());
    }

    /**
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     */
    public function testGetName(): void
    {
        self::assertSame('NAME', $this->variable->getName());
    }

    /**
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame('V4lu3', $this->variable->getValue());
    }
}
