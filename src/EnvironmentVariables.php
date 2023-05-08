<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Generator;
use Ghostwriter\Environment\Exception\EnvironmentException;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use RuntimeException;

/**
 * Maps environment variables.
 *
 * @see \Ghostwriter\Environment\Tests\Unit\EnvironmentTest
 */
final class EnvironmentVariables implements EnvironmentVariablesInterface
{
    /** @var non-empty-array<non-empty-string,non-empty-string> $variables */
    private array $variables;

    /**
     * Environment Variables provided to the script via the environment `$_ENV` and `$_SERVER`.
     *
     * @param non-empty-array<non-empty-string,non-empty-string>|null $serverVariables `$_SERVER` variables
     * @param non-empty-array<non-empty-string,non-empty-string>|null $environmentVariables `$_ENV` variables
     */
    public function __construct(
            array|null $serverVariables = null,
            array|null $environmentVariables = null
    ) {
        $environmentVariables ??= ($_ENV === [] && function_exists('getenv')) ? getenv() : $_ENV;

        if ($environmentVariables === []) {
            $variablesOrder = ini_get('variables_order');
            if ($variablesOrder === false || ! str_contains($variablesOrder, 'E')) {
                throw new EnvironmentException(sprintf(
                    "%s\n%s\n%s",
                    'Cannot get a list of the current environment variables.',
                    'Make sure the `variables_order` variable in php.ini contains the letter "E".',
                    'See: https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                ));
            }
        }

        $serverVariables ??= $_SERVER;

        /** @var non-empty-array<non-empty-string,non-empty-string> $this->variables */
        $this->variables = array_filter(
            $serverVariables + $environmentVariables,
            static fn (mixed $name, mixed $value): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    public function get(string $name, string|null $default = null): string
    {
        $variable = $this->variables[$name] ?? $default;

        if ($variable === null) {
            throw new NotFoundException();
        }

        return $variable;
    }

    public function getIterator(): Generator
    {
        yield from $this->variables;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    public function set(string $name, string $value): void
    {
        self::validVariable($name, $value);

        $this->variables[$name] = $_ENV[$name] = $_SERVER[$name] = $value;
    }

    public function toArray(): array
    {
        return $this->variables;
    }

    public function unset(string $name): void
    {
        if (! $this->has($name)) {
            throw new NotFoundException();
        }

        unset($_ENV[$name], $_SERVER[$name], $this->variables[$name]);
    }

    /**
     * @psalm-assert-if-true non-empty-string $name
     * @psalm-assert-if-true non-empty-string $value
     *
     * @param non-empty-string $name
     * @param non-empty-string $value
     */
    private static function validVariable(string $name, string $value): void
    {
        $trimmedName = trim($name);

        match (true) {
            $trimmedName === '' => throw new InvalidNameException('Variable $name MUST be a non-empty-string.'),
            $trimmedName !== $name => throw new InvalidNameException(
                'Variable $name MUST not contain leading or trailing whitespace.'
            ),
            str_contains($name, '=') => throw new InvalidNameException(
                'Variable $name MUST not contain "=" an equal sign.'
            ),
            str_contains($name, "\0") => throw new InvalidNameException(
                'Variable $name MUST not contain "\0" a null byte character.'
            ),
            default => $name
        };

        match (true) {
            trim($value) !== $value => throw new InvalidValueException(
                'Variable $value MUST not contain leading or trailing whitespace.'
            ),
            str_contains($value, "\0") => throw new InvalidValueException(
                'Variable $value MUST not contain "\0" a null byte character.'
            ),
            default => $value
        };
    }
}
