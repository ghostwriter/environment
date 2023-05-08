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
final class EnvironmentVariablesTest extends TestCase
{
    /** @var non-empty-string */
    private const NAME = 'BLM';

    /** @var non-empty-string */
    private const VALUE = '#BlackLivesMatter';

    /** @var non-empty-array<non-empty-string,non-empty-string> */
    private array $backupENV;

    /** @var non-empty-array<non-empty-string,non-empty-string> */
    private array $backupEnvironmentVariables;

    /** @var non-empty-array<non-empty-string,non-empty-string> */
    private array $backupSERVER;

    private EnvironmentVariables $environmentVariables;

    protected function setUp(): void
    {
        /** @var non-empty-array<non-empty-string,non-empty-string> $this->backupSERVER */
        $this->backupSERVER = $_SERVER;

        /** @var non-empty-array<non-empty-string,non-empty-string> $this->backupENV */
        $this->backupENV = ($_ENV === [] && function_exists('getenv')) ? getenv() : $_ENV;

        if ($this->backupENV === []) {
            $variablesOrder = ini_get('variables_order');
            if ($variablesOrder === false || ! str_contains($variablesOrder, 'E')) {
                self::markTestSkipped(
                    'Cannot get a list of the current environment variables. '
                    . 'Make sure the `variables_order` variable in php.ini '
                    . 'contains the letter "E". https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                );
            }
        }

        /** @var non-empty-array<non-empty-string,non-empty-string> $this->backupEnvironmentVariables */
        $this->backupEnvironmentVariables = array_filter(
            $this->backupSERVER + $this->backupENV,
            static fn (mixed $name, mixed $value): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );

        $this->environmentVariables = new EnvironmentVariables();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->backupSERVER;

        $_ENV = $this->backupENV;

        unset($this->environmentVariables);
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
        /** @var non-empty-array<non-empty-string,non-empty-string> $serverVariables */
        $serverVariables = [];

        $environmentVariables = new EnvironmentVariables($serverVariables, $this->backupEnvironmentVariables);
        self::assertInstanceOf(EnvironmentVariablesInterface::class, $environmentVariables);
        self::assertSame($this->backupEnvironmentVariables, $environmentVariables->toArray());

        $environmentVariables = new EnvironmentVariables($this->backupSERVER, $this->backupENV);
        self::assertInstanceOf(EnvironmentVariablesInterface::class, $environmentVariables);
        self::assertSame($this->backupEnvironmentVariables, $environmentVariables->toArray());
    }

    public function testConstructThrowsRuntimeException(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectException(RuntimeException::class);

        /** @var non-empty-array<non-empty-string,non-empty-string> $serverVariables */
        $serverVariables = [];

        /** @var non-empty-array<non-empty-string,non-empty-string> $environmentVariables */
        $environmentVariables = [];

        self::assertInstanceOf(
            EnvironmentVariablesInterface::class,
            new EnvironmentVariables($serverVariables, $environmentVariables)
        );
    }

    public function testCount(): void
    {
        self::assertNotEmpty($this->environmentVariables);
    }

    public function testGetEnvironmentVariable(): void
    {
        $count = $this->environmentVariables->count();
        self::assertFalse($this->environmentVariables->has('GET_FOO'));
        $this->environmentVariables->set('GET_FOO', 'BAR');
        self::assertCount($count + 1, $this->environmentVariables);
        self::assertTrue($this->environmentVariables->has('GET_FOO'));
        self::assertSame('BAR', $this->environmentVariables->get('GET_FOO'));
        self::assertFalse($this->environmentVariables->has('FOOBAR'));
        self::assertSame('BAZ', $this->environmentVariables->get('FOOBAR', 'BAZ'));
        self::assertTrue(true);
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(IteratorAggregate::class, $this->environmentVariables);
        self::assertCount(iterator_count($this->environmentVariables->getIterator()), $this->environmentVariables);
    }

    public function testGetNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->environmentVariables->get('NOT_FOUND');
    }

    public function testGetServerVariable(): void
    {
        $count = $this->environmentVariables->count();
        $environmentVariables = $this->environmentVariables->toArray();

        self::assertFalse($this->environmentVariables->has('GetServerVariable'));

        $this->environmentVariables->set('GetServerVariable', 'ServerVariable');

        self::assertCount($count+1, $this->environmentVariables);

        self::assertTrue($this->environmentVariables->has('GetServerVariable'));
        self::assertSame('ServerVariable', $this->environmentVariables->get('GetServerVariable'));

        $this->environmentVariables->unset('GetServerVariable');

        self::assertFalse($this->environmentVariables->has('GetServerVariable'));
        self::assertSame('NULL', $this->environmentVariables->get('GetServerVariable', 'NULL'));

        self::assertCount($count, $this->environmentVariables);
        self::assertSame($environmentVariables, $this->environmentVariables->toArray());
    }

    public function testGetVariable(): void
    {
        $count = $this->environmentVariables->count();
        self::assertFalse($this->environmentVariables->has('GET_FOO'));
        $this->environmentVariables->set('GET_FOO', 'BAR');
        self::assertCount($count+1, $this->environmentVariables);
        self::assertTrue($this->environmentVariables->has('GET_FOO'));
        self::assertSame('BAR', $this->environmentVariables->get('GET_FOO'));
        self::assertFalse($this->environmentVariables->has('FOOBAR'));
        self::assertSame('BAZ', $this->environmentVariables->get('FOOBAR', 'BAZ'));
    }

    public function testHasEnvironmentVariable(): void
    {
        self::assertFalse($this->environmentVariables->has('HAS_FOO'));
        $this->environmentVariables->set('HAS_FOO', 'BAR');
        self::assertTrue($this->environmentVariables->has('HAS_FOO'));
    }

    /**
     * @param non-empty-string             $name
     * @param non-empty-string             $value
     * @param null|class-string<Throwable> $expectedException
     */
    #[DataProvider('environmentVariablesProvider')]
    public function testHasGetSetUnset(string $name, string $value, string|null $expectedException = null): void
    {
        self::assertFalse($this->environmentVariables->has($name));

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->environmentVariables->set($name, $value);

        self::assertTrue($this->environmentVariables->has($name));
        self::assertSame($value, $this->environmentVariables->get($name));

        $this->environmentVariables->unset($name);

        self::assertFalse($this->environmentVariables->has($name));
    }

    public function testSetEnvironmentVariable(): void
    {
        $count = $this->environmentVariables->count();
        self::assertFalse($this->environmentVariables->has('SET_FOO'));
        $this->environmentVariables->set('SET_FOO', 'SET_FOO_BAR');
        self::assertTrue($this->environmentVariables->has('SET_FOO'));
        self::assertSame('SET_FOO_BAR', $this->environmentVariables->get('SET_FOO'));
        self::assertCount($count+1, $this->environmentVariables);
    }

    public function testToArray(): void
    {
        self::assertSame($this->backupEnvironmentVariables, $this->environmentVariables->toArray());
    }

    public function testUnsetEnvironmentVariable(): void
    {
        $count = $this->environmentVariables->count();
        self::assertCount($count, $this->environmentVariables);
        self::assertFalse($this->environmentVariables->has(self::NAME));
        $this->environmentVariables->set(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environmentVariables);
        self::assertTrue($this->environmentVariables->has(self::NAME));
        self::assertSame(self::VALUE, $this->environmentVariables->get(self::NAME));
        $this->environmentVariables->unset(self::NAME);
        self::assertCount($count, $this->environmentVariables);
        self::assertFalse($this->environmentVariables->has(self::NAME));
    }

    public function testUnsetEnvironmentVariableThrowsNotFoundException(): void
    {
        $count = $this->environmentVariables->count();
        self::assertCount($count, $this->environmentVariables);
        self::assertFalse($this->environmentVariables->has('NOT_FOUND'));
        $this->expectException(NotFoundException::class);
        $this->environmentVariables->unset('NOT_FOUND');
        self::assertCount($count, $this->environmentVariables);
    }

    public function testUnsetServerVariable(): void
    {
        $count = $this->environmentVariables->count();
        self::assertCount($count, $this->environmentVariables);
        self::assertFalse($this->environmentVariables->has(self::NAME));
        $this->environmentVariables->set(self::NAME, self::VALUE);
        self::assertCount($count+1, $this->environmentVariables);
        self::assertTrue($this->environmentVariables->has(self::NAME));
        self::assertSame(self::VALUE, $this->environmentVariables->get(self::NAME));
        $this->environmentVariables->unset(self::NAME);
        self::assertCount($count, $this->environmentVariables);
        self::assertFalse($this->environmentVariables->has(self::NAME));
    }
}
