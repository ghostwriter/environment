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
    /** @var string */
    private const NAME = 'VARIABLE_NAME';

    /** @var string */
    private const VALUE = 'VARIABLE_VALUE';

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
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     */
    public function testCount(): void
    {
        self::assertCount($this->environment->count(), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testGetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->getEnvironmentVariable('NOT_FOUND');
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testGetVariable(): void
    {
        $this->environment->setEnvironmentVariable('FOO', 'BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('FOO'));
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('FOO'));
        self::assertFalse($this->environment->hasEnvironmentVariable('FOOBAR'));
        self::assertSame('BAZ', $this->environment->getEnvironmentVariable('FOOBAR', 'BAZ'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
    ┴
     */
    public function testHasVariable(): void
    {
        $this->environment->setEnvironmentVariable('FOO', 'BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
    ┴
     */
    public function testSetVariable(): void
    {
        $this->environment->setEnvironmentVariable('FOO', 'BAR');
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testToArray(): void
    {
        self::assertSame($this->environmentVariables, $this->environment->toArray());
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testUnsetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->unsetEnvironmentVariable('NOT_FOUND');
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testUnsetVariable(): void
    {
        self::assertFalse($this->environment->hasEnvironmentVariable(self::NAME));
        $this->environment->setEnvironmentVariable(self::NAME, self::VALUE);
        self::assertTrue($this->environment->hasEnvironmentVariable(self::NAME));
        self::assertSame(self::VALUE, $this->environment->getEnvironmentVariable(self::NAME));
        $this->environment->unsetEnvironmentVariable(self::NAME);
        self::assertFalse($this->environment->hasEnvironmentVariable(self::NAME));
    }
}
