<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\Environment\EnvironmentVariableInterface;
use Ghostwriter\Environment\EnvironmentVariable;
use Ghostwriter\Environment\Exception\InvalidEnvironmentVariableNameException;
use Ghostwriter\Environment\Exception\InvalidEnvironmentVariableValueException;

/**
 *@coversDefaultClass \Ghostwriter\Environment\EnvironmentVariable
 *
 * @internal
 *
 * @small
 */
final class EnvironmentVariableTest extends AbstractTestCase
{
    private EnvironmentVariableInterface $environmentVariable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->environmentVariable = new EnvironmentVariable('NAME', 'V4lu3');
    }

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
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getValue
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
            $this->expectException(InvalidEnvironmentVariableNameException::class);
        }

        $trimmedValue = trim($value);
        if (
            '' === $trimmedValue ||
            $value !== $trimmedValue ||
            str_contains($value, "\0")
        ) {
            $this->expectException(InvalidEnvironmentVariableValueException::class);
        }

        self::assertSame($name, (new EnvironmentVariable($name, $value))->getName());
        self::assertSame($value, (new EnvironmentVariable($name, $value))->getValue());
    }

    /**
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getName
     */
    public function testGetName(): void
    {
        self::assertSame('NAME', $this->environmentVariable->getName());
    }

    /**
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame('V4lu3', $this->environmentVariable->getValue());
    }
}
