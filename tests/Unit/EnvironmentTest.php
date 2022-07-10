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
 *
 * @psalm-suppress MissingConstructor
 */
final class EnvironmentTest extends AbstractTestCase
{
    /** @var string */
    private const NAME = 'VARIABLE_NAME';

    /** @var string */
    private const VALUE = 'VARIABLE_VALUE';

    private EnvironmentInterface $environment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->environment = new Environment();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->environment);
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
        self::assertCount(count(
            array_filter(
                $this->backupEnvironmentVariables,
                static fn ($value, $name): bool => is_string($name) && is_string($value),
                ARRAY_FILTER_USE_BOTH
            )
        )+count(
            array_filter(
                $this->backupServerVariables,
                static fn ($value, $name): bool => is_string($name) && is_string($value),
                ARRAY_FILTER_USE_BOTH
            )
        ), $this->environment);
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
        self::assertFalse($this->environment->hasEnvironmentVariable('GET_FOO'));
        $this->environment->setEnvironmentVariable('GET_FOO', 'BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('GET_FOO'));
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('GET_FOO'));
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
        $this->environment->setEnvironmentVariable('HAS_FOO', 'BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('HAS_FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     */
    public function testSetVariable(): void
    {
        self::assertFalse($this->environment->hasEnvironmentVariable('SET_FOO'));
        $this->environment->setEnvironmentVariable('SET_FOO', 'SET_FOO_BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('SET_FOO'));
        self::assertSame('SET_FOO_BAR', $this->environment->getEnvironmentVariable('SET_FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::toArray
     */
    public function testToArray(): void
    {
        self::assertSame(array_filter(
            array_merge($this->backupEnvironmentVariables, $this->backupServerVariables),
            static fn ($value, $name): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        ), $this->environment->toArray());
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
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertVariableValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
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
