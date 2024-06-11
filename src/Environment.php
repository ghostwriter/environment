<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Generator;
use Ghostwriter\Environment\Exception\EnvironmentException;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use Ghostwriter\Environment\Interface\EnvironmentInterface;
use Override;

use const ARRAY_FILTER_USE_BOTH;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function function_exists;
use function getenv;
use function ini_get;
use function is_string;
use function iterator_count;
use function sprintf;
use function str_contains;
use function trim;

/**
 * Maps environment variables.
 *
 * @see \Tests\Unit\EnvironmentTest
 */
final class Environment implements EnvironmentInterface
{
    /** @var non-empty-array<non-empty-string,non-empty-string> */
    private array $variables;

    /**
     * Environment Variables provided to the script via the environment `$_ENV` and `$_SERVER`.
     *
     * @param null|non-empty-array<non-empty-string,non-empty-string> $serverVariables      `$_SERVER` variables
     * @param null|non-empty-array<non-empty-string,non-empty-string> $environmentVariables `$_ENV` variables
     *
     * @throws EnvironmentException if the environment variables cannot be retrieved
     */
    public function __construct(null|array $serverVariables = null, null|array $environmentVariables = null)
    {
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

        /** @var non-empty-array<non-empty-string,non-empty-string> $variables */
        $variables = array_filter(
            array_merge($serverVariables, $environmentVariables),
            static fn (mixed $name, mixed $value): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );

        $this->variables = $variables;
    }

    #[Override]
    public function count(): int
    {
        return iterator_count($this);
    }

    /**
     * @throws NotFoundException if variable $name does not exist and $default is not a string
     */
    #[Override]
    public function get(string $name, null|string $default = null): string
    {
        $variable = $this->variables[$name] ?? $default;

        if ($variable === null) {
            throw new NotFoundException();
        }

        return $variable;
    }

    #[Override]
    public function getIterator(): Generator
    {
        yield from $this->variables;
    }

    #[Override]
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    #[Override]
    public function set(string $name, string $value): void
    {
        self::validVariable($name, $value);

        $_ENV[$name] = $_SERVER[$name] = $this->variables[$name] = $value;
    }

    #[Override]
    public function toArray(): array
    {
        return $this->variables;
    }

    #[Override]
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
            default => $name,

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
        };

        match (true) {
            default => $value,

            trim($value) !== $value => throw new InvalidValueException(
                'Variable $value MUST not contain leading or trailing whitespace.'
            ),

            str_contains($value, "\0") => throw new InvalidValueException(
                'Variable $value MUST not contain "\0" a null byte character.'
            )
        };
    }
}
