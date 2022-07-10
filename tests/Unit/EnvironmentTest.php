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
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     */
    public function testCount(): void
    {
        self::assertCount($this->environment->count(), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environment);
        self::assertCount(iterator_count($this->environment->getIterator()), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
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
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     */
    public function testGetVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->hasEnvironmentVariable('GET_FOO'));
        $this->environment->setEnvironmentVariable('GET_FOO', 'BAR');
        self::assertCount($count+1, $this->environment);
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
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
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
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getServerVariable
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Environment::unsetServerVariable
     */
    public function testServerVariable(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasServerVariable(self::NAME));
        $this->environment->setServerVariable(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->hasServerVariable(self::NAME));
        self::assertSame(self::VALUE, $this->environment->getServerVariable(self::NAME));
        $this->environment->unsetServerVariable(self::NAME);
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasServerVariable(self::NAME));
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
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
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
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
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::toArray
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
     */
    public function testUnsetEnvironmentVariable(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasEnvironmentVariable(self::NAME));
        $this->environment->setEnvironmentVariable(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->hasEnvironmentVariable(self::NAME));
        self::assertSame(self::VALUE, $this->environment->getEnvironmentVariable(self::NAME));
        $this->environment->unsetEnvironmentVariable(self::NAME);
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasEnvironmentVariable(self::NAME));
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::filterStringNameAndValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::getIterator
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
     */
    public function testUnsetNotFoundException(): void
    {
        self::assertFalse($this->environment->hasEnvironmentVariable('NOT_FOUND'));
        $this->expectException(NotFoundException::class);
        $this->environment->unsetEnvironmentVariable('NOT_FOUND');
    }
}
