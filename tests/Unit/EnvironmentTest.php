<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Ghostwriter\Environment\Contract\EnvironmentInterface;
use Ghostwriter\Environment\Environment;
use Ghostwriter\Environment\Exception\NotFoundException;
use IteratorAggregate;

/**
 * @coversDefaultClass \Ghostwriter\Environment\Environment
 *
 * @internal
 *
 * @small
 */
final class EnvironmentTest extends AbstractTestCase
{
    private EnvironmentInterface $environment;

    /**
     * @var array<string,string>
     */
    private array $environmentVariables = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->environment = new Environment();

        /** @var array<string,string> $this->environmentVariables */
        $this->environmentVariables = array_filter(
            array_merge(function_exists('getenv') ? (getenv() ?: []) : [], $_ENV, $_SERVER),
            static fn ($value, $name): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     */
    public function testCount(): void
    {
        self::assertCount(count($this->environmentVariables), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testGetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->getVariable('NOT_FOUND');
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getVariable
     * @covers \Ghostwriter\Environment\Environment::hasVariable
     * @covers \Ghostwriter\Environment\Environment::setVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testGetVariable(): void
    {
        $this->environment->setVariable('FOO', 'BAR');
        self::assertTrue($this->environment->hasVariable('FOO'));
        self::assertSame('BAR', $this->environment->getVariable('FOO'));
        self::assertFalse($this->environment->hasVariable('FOOBAR'));
        self::assertSame('BAZ', $this->environment->getVariable('FOOBAR', 'BAZ'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasVariable
     * @covers \Ghostwriter\Environment\Environment::setVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
    ┴
     */
    public function testHasVariable(): void
    {
        $this->environment->setVariable('FOO', 'BAR');
        self::assertTrue($this->environment->hasVariable('FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getVariable
     * @covers \Ghostwriter\Environment\Environment::setVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
    ┴
     */
    public function testSetVariable(): void
    {
        $this->environment->setVariable('FOO', 'BAR');
        self::assertSame('BAR', $this->environment->getVariable('FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testToArray(): void
    {
        self::assertSame($this->environmentVariables, $this->environment->toArray());
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getVariable
     * @covers \Ghostwriter\Environment\Environment::unsetVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testUnsetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->unsetVariable('NOT_FOUND');
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getVariable
     * @covers \Ghostwriter\Environment\Environment::hasVariable
     * @covers \Ghostwriter\Environment\Environment::setVariable
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Environment::unsetVariable
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
     * @covers \Ghostwriter\Environment\Variable::getValue
     */
    public function testUnsetVariable(): void
    {
        self::assertFalse($this->environment->hasVariable('UNSET'));
        $this->environment->setVariable('UNSET', 'VALUE');
        self::assertTrue($this->environment->hasVariable('UNSET'));
        self::assertSame('VALUE', $this->environment->getVariable('UNSET'));
        $this->environment->unsetVariable('UNSET');
        self::assertFalse($this->environment->hasVariable('UNSET'));
    }
}
