<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use Generator;
use Ghostwriter\Environment\EnvironmentVariables;
use Ghostwriter\Environment\EnvironmentVariablesInterface;
use Ghostwriter\Environment\Exception\EnvironmentException;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

#[CoversClass(EnvironmentVariables::class)]
final class EnvironmentTest extends TestCase
{
    /** @var string */
    private const NAME = 'VARIABLE_NAME';

    /** @var string */
    private const VALUE = 'VARIABLE_VALUE';

    /**
     * @var array<string,string>
     */
    protected array $backupEnvironmentVariables = [];

    private EnvironmentVariables $environment;

    protected function setUp(): void
    {
        /** @var array<string,string> $environment */
        $environment = ($_ENV === [] && function_exists('getenv')) ? getenv() : $_ENV;

        if ($environment === []) {
            $variablesOrder = ini_get('variables_order');
            if ($variablesOrder === false || ! str_contains($variablesOrder, 'E')) {
                self::markTestSkipped(
                    'Cannot get a list of the current environment variables. '
                    . 'Make sure the `variables_order` variable in php.ini '
                    . 'contains the letter "E". https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                );
            }
        }

        /** @var array<string,string> $this->backupEnvironmentVariables */
        $this->backupEnvironmentVariables = array_filter(
            $_SERVER + $environment,
            static fn (mixed $name, mixed  $value): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );

        $this->environment = new EnvironmentVariables();
    }

    protected function tearDown(): void
    {
        $_SERVER = $_ENV = $this->backupEnvironmentVariables;

        unset($this->environment);
    }

    /**
     * @return Generator<string,array<array-key,non-empty-string|string>>
     */
    public static function environmentVariablesProvider(): Generator
    {
        yield from [
            'default' => ['name', 'value'],
            'empty-name' => ['', 'value', InvalidNameException::class],
            'empty-value' => ['name', ''],
            'untrimmed-name' => [' name ', 'value', InvalidNameException::class],
            'untrimmed-value' => ['name', ' value ', InvalidValueException::class],
            'name-contains-equal-sign' => ['na=me', 'value', InvalidNameException::class],
            'value-contains-equal-sign' => ['name', 'val=ue'],
            'name-contains-NULL-byte' => ["na\0me", 'value', InvalidNameException::class],
            'value-contains-NULL-byte' => ['name', "val\0ue", InvalidValueException::class],
        ];
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(EnvironmentVariablesInterface::class, new EnvironmentVariables());
    }
    public function testConstructThrowsRuntimeException(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectException(RuntimeException::class);

        /** @var non-empty-array<non-empty-string,non-empty-string> $environmentVariables */
        $environmentVariables == [];

        self::assertInstanceOf(
            EnvironmentVariablesInterface::class,
            new EnvironmentVariables(null, $environmentVariables)
        );
    }

    public function testCount(): void
    {
        self::assertNotEmpty($this->environment);
    }

    public function testGetEnvironmentVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->has('GET_FOO'));
        $this->environment->set('GET_FOO', 'BAR');
        self::assertCount($count + 1, $this->environment);
        self::assertTrue($this->environment->has('GET_FOO'));
        self::assertSame('BAR', $this->environment->get('GET_FOO'));
        self::assertFalse($this->environment->has('FOOBAR'));
        self::assertSame('BAZ', $this->environment->get('FOOBAR', 'BAZ'));
        self::assertTrue(true);
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environment);
        self::assertCount(iterator_count($this->environment->getIterator()), $this->environment);
    }

    public function testGetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environment->get('NOT_FOUND');
    }

    public function testGetServerVariable(): void
    {
        $count = $this->environment->count();
        $environmentVariables = $this->environment->toArray();

        self::assertFalse($this->environment->has('GetServerVariable'));

        $this->environment->set('GetServerVariable', 'ServerVariable');

        self::assertCount($count+1, $this->environment);

        self::assertTrue($this->environment->has('GetServerVariable'));
        self::assertSame('ServerVariable', $this->environment->get('GetServerVariable'));

        $this->environment->unset('GetServerVariable');

        self::assertFalse($this->environment->has('GetServerVariable'));
        self::assertSame('NULL', $this->environment->get('GetServerVariable', 'NULL'));

        self::assertCount($count, $this->environment);
        self::assertSame($environmentVariables, $this->environment->toArray());
    }

    public function testGetVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->has('GET_FOO'));
        $this->environment->set('GET_FOO', 'BAR');
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->has('GET_FOO'));
        self::assertSame('BAR', $this->environment->get('GET_FOO'));
        self::assertFalse($this->environment->has('FOOBAR'));
        self::assertSame('BAZ', $this->environment->get('FOOBAR', 'BAZ'));
    }

    public function testHasEnvironmentVariable(): void
    {
        self::assertFalse($this->environment->has('HAS_FOO'));
        $this->environment->set('HAS_FOO', 'BAR');
        self::assertTrue($this->environment->has('HAS_FOO'));
    }

    /**
     * @param non-empty-string             $name
     * @param non-empty-string             $value
     * @param null|class-string<Throwable> $expectedException
     */
    #[DataProvider('environmentVariablesProvider')]
    public function testHasGetSetUnset(string $name, string $value, string|null $expectedException = null): void
    {
        self::assertFalse($this->environment->has($name));

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->environment->set($name, $value);

        self::assertTrue($this->environment->has($name));
        self::assertSame($value, $this->environment->get($name));

        $this->environment->unset($name);

        self::assertFalse($this->environment->has($name));
    }

    public function testSetEnvironmentVariable(): void
    {
        $count = $this->environment->count();
        self::assertFalse($this->environment->has('SET_FOO'));
        $this->environment->set('SET_FOO', 'SET_FOO_BAR');
        self::assertTrue($this->environment->has('SET_FOO'));
        self::assertSame('SET_FOO_BAR', $this->environment->get('SET_FOO'));
        self::assertCount($count+1, $this->environment);
    }

    public function testToArray(): void
    {
        self::assertSame($this->backupEnvironmentVariables, $this->environment->toArray());
    }

    public function testUnsetEnvironmentVariable(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->has(self::NAME));
        $this->environment->set(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->has(self::NAME));
        self::assertSame(self::VALUE, $this->environment->get(self::NAME));
        $this->environment->unset(self::NAME);
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->has(self::NAME));
    }

    public function testUnsetEnvironmentVariableThrowsNotFoundException(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->has('NOT_FOUND'));
        $this->expectException(NotFoundException::class);
        $this->environment->unset('NOT_FOUND');
        self::assertCount($count, $this->environment);
    }

    public function testUnsetServerVariable(): void
    {
        $count = $this->environment->count();
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->has(self::NAME));
        $this->environment->set(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environment);
        self::assertTrue($this->environment->has(self::NAME));
        self::assertSame(self::VALUE, $this->environment->get(self::NAME));
        $this->environment->unset(self::NAME);
        self::assertCount($count, $this->environment);
        self::assertFalse($this->environment->has(self::NAME));
    }
}
