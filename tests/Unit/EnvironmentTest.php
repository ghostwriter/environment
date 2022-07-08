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

    protected function setUp(): void
    {
        parent::setUp();

        $this->environment = new Environment();
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
        self::assertCount(count(getenv()), $this->environment);
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
     * @covers \Ghostwriter\Environment\Variable::__construct
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableName
     * @covers \Ghostwriter\Environment\Variable::assertValidVariableValue
     * @covers \Ghostwriter\Environment\Variable::getName
    ┴
     */
    public function testHasVariable(): void
    {
        self::assertTrue($this->environment->hasVariable('TMPDIR'));
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
        self::assertSame(getenv(), $this->environment->toArray());
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
        self::assertSame(getenv(), $this->environment->toArray());
        $this->environment->setVariable('UNSET', 'VALUE');
        self::assertSame(getenv(), $this->environment->toArray());
        self::assertSame('VALUE', $this->environment->getVariable('UNSET'));
        self::assertTrue($this->environment->hasVariable('UNSET'));
        $this->environment->unsetVariable('UNSET');
        self::assertFalse($this->environment->hasVariable('UNSET'));
        self::assertSame(getenv(), $this->environment->toArray());
    }
}
