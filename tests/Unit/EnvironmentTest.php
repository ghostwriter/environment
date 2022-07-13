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
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariables
     * @covers \Ghostwriter\Environment\Environment::getServerVariables
     */
    public function testConstruct(): void
    {
        self::assertSame($this->backupEnvironmentVariables, $this->environment->getEnvironmentVariables());
        self::assertSame($this->backupServerVariables, $this->environment->getServerVariables());
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getIterator
     */
    public function testCount(): void
    {
        self::assertCount($this->environment->count(), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     */
    public function testGetEnvironmentVariable(): void
    {
        $count = $this->environment->count();

        self::assertFalse($this->environment->hasEnvironmentVariable('GET_FOO'));
        $this->environment->setEnvironmentVariable('GET_FOO', 'BAR');
        self::assertCount($count + 1, $this->environment);
        self::assertTrue($this->environment->hasEnvironmentVariable('GET_FOO'));
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('GET_FOO'));
        self::assertFalse($this->environment->hasEnvironmentVariable('FOOBAR'));
        self::assertSame('BAZ', $this->environment->getEnvironmentVariable('FOOBAR', 'BAZ'));
        self::assertTrue(true);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     */
    public function testGetEnvironmentVariables(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->hasEnvironmentVariable('GET_FOO'));
        $this->environment->setEnvironmentVariable('GET_FOO', 'BAR');
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->hasEnvironmentVariable('GET_FOO'));
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('GET_FOO'));
        self::assertFalse($this->environment->hasEnvironmentVariable('FOOBAR'));
        self::assertSame('BAZ', $this->environment->getEnvironmentVariable('FOOBAR', 'BAZ'));
        self::assertTrue(true);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environment);
        self::assertCount(iterator_count($this->environment->getIterator()), $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     */
    public function testGetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->getEnvironmentVariable('NOT_FOUND');
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariables
     * @covers \Ghostwriter\Environment\Environment::getServerVariable
     * @covers \Ghostwriter\Environment\Environment::getServerVariables
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
     * @covers \Ghostwriter\Environment\Environment::unsetServerVariable
     */
    public function testGetServerVariable(): void
    {
        $count = $this->environment->count();
        $environmentVariables = $this->environment->getEnvironmentVariables();
        $serverVariables = $this->environment->getServerVariables();

        self::assertFalse($this->environment->hasServerVariable('GetServerVariable'));

        $this->environment->setServerVariable('GetServerVariable', 'ServerVariable');

        self::assertCount($count+1, $this->environment);

        self::assertTrue($this->environment->hasServerVariable('GetServerVariable'));

        self::assertNotSame($serverVariables, $this->environment->getServerVariables());

        self::assertSame('ServerVariable', $this->environment->getServerVariable('GetServerVariable'));

        $this->environment->unsetServerVariable('GetServerVariable');

        self::assertFalse($this->environment->hasServerVariable('GetServerVariable'));
        self::assertSame('NULL', $this->environment->getServerVariable('GetServerVariable', 'NULL'));

        self::assertCount($count, $this->environment);
        self::assertSame($environmentVariables, $this->environment->getEnvironmentVariables());
        self::assertSame($serverVariables, $this->environment->getServerVariables());
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     */
    public function testGetServerVariables(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->hasEnvironmentVariable('GET_FOO'));
        $this->environment->setEnvironmentVariable('GET_FOO', 'BAR');
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->hasEnvironmentVariable('GET_FOO'));
        self::assertSame('BAR', $this->environment->getEnvironmentVariable('GET_FOO'));
        self::assertFalse($this->environment->hasEnvironmentVariable('FOOBAR'));
        self::assertSame('BAZ', $this->environment->getEnvironmentVariable('FOOBAR', 'BAZ'));
        self::assertTrue(true);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
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
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
    ┴
     */
    public function testHasEnvironmentVariable(): void
    {
        self::assertFalse($this->environment->hasEnvironmentVariable('HAS_FOO'));
        $this->environment->setEnvironmentVariable('HAS_FOO', 'BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('HAS_FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
     */
    public function testHasServerVariable(): void
    {
        self::assertFalse($this->environment->hasServerVariable('HAS_FOO'));
        $this->environment->setServerVariable('HAS_FOO', 'BAR');
        self::assertTrue($this->environment->hasServerVariable('HAS_FOO'));
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getServerVariable
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
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
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
     */
    public function testSetEnvironmentVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->hasEnvironmentVariable('SET_FOO'));
        $this->environment->setEnvironmentVariable('SET_FOO', 'SET_FOO_BAR');
        self::assertTrue($this->environment->hasEnvironmentVariable('SET_FOO'));
        self::assertSame('SET_FOO_BAR', $this->environment->getEnvironmentVariable('SET_FOO'));
        self::assertCount($count+1, $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getServerVariable
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
     */
    public function testSetServerVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->hasServerVariable('SET_FOO'));
        $this->environment->setServerVariable('SET_FOO', 'SET_FOO_BAR');
        self::assertTrue($this->environment->hasServerVariable('SET_FOO'));
        self::assertSame('SET_FOO_BAR', $this->environment->getServerVariable('SET_FOO'));
        self::assertCount($count+1, $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
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
     * @covers \Ghostwriter\Environment\Environment::getEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setEnvironmentVariable
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
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::hasEnvironmentVariable
     * @covers \Ghostwriter\Environment\Environment::unsetEnvironmentVariable
     */
    public function testUnsetEnvironmentVariableNotFoundException(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasEnvironmentVariable('NOT_FOUND'));
        $this->expectException(NotFoundException::class);
        $this->environment->unsetEnvironmentVariable('NOT_FOUND');
        self::assertCount($count, $this->environment);
    }

    /**
     * @covers \Ghostwriter\Environment\AbstractVariable::__construct
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidName
     * @covers \Ghostwriter\Environment\AbstractVariable::assertValidValue
     * @covers \Ghostwriter\Environment\AbstractVariable::getName
     * @covers \Ghostwriter\Environment\AbstractVariable::getValue
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::getServerVariable
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::mutate
     * @covers \Ghostwriter\Environment\Environment::setServerVariable
     * @covers \Ghostwriter\Environment\Environment::unsetServerVariable
     */
    public function testUnsetServerVariable(): void
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
     * @covers \Ghostwriter\Environment\Environment::__construct
     * @covers \Ghostwriter\Environment\Environment::count
     * @covers \Ghostwriter\Environment\Environment::hasServerVariable
     * @covers \Ghostwriter\Environment\Environment::unsetServerVariable
     */
    public function testUnsetServerVariableNotFoundException(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->hasServerVariable('NOT_FOUND'));
        $this->expectException(NotFoundException::class);
        $this->environment->unsetServerVariable('NOT_FOUND');
        self::assertCount($count, $this->environment);
    }
}
