<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\Environment\EnvironmentVariableInterface;
use Ghostwriter\Environment\EnvironmentVariable;

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

    /**
     * @covers \Ghostwriter\Environment\EnvironmentVariable::__construct
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableName
     * @covers \Ghostwriter\Environment\EnvironmentVariable::assertValidEnvironmentVariableValue
     * @covers \Ghostwriter\Environment\EnvironmentVariable::getName
     */
    public function testConstruct(): void
    {
        self::assertSame('KEY', (new EnvironmentVariable('KEY', 'VALUE'))->getName());
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
