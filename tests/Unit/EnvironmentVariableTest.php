<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\EnvironmentVariable;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;

/**
 * @coversDefaultClass \Ghostwriter\Environment\EnvironmentVariable
 *
 * @internal
 *
 * @small
 */
final class EnvironmentVariableTest extends AbstractTestCase
{
    /** @var string */
    private const NAME = 'VARIABLE_NAME';

    /** @var string */
    private const VALUE = 'VARIABLE_VALUE';

    private VariableInterface $variable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->variable = $this->createVariable(self::NAME, self::VALUE);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->variable);
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
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getValue
     *
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

        self::assertSame($name, $this->createVariable($name, $value)->getName());
        self::assertSame($value, $this->createVariable($name, $value)->getValue());
    }

    /**
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getName
     */
    public function testGetName(): void
    {
        self::assertSame(self::NAME, $this->variable->getName());
    }

    /**
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame(self::VALUE, $this->variable->getValue());
    }

    private function createVariable(string $name, string $value): EnvironmentVariable
    {
        return new EnvironmentVariable($name, $value);
    }
}
