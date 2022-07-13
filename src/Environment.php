<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Closure;
use Generator;
use Ghostwriter\Collection\Collection;
use Ghostwriter\Environment\Contract\EnvironmentInterface;
use Ghostwriter\Environment\Contract\EnvironmentVariableInterface;
use Ghostwriter\Environment\Contract\ServerVariableInterface;
use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\NotFoundException;
use RuntimeException;
use Traversable;

/**
 * Maps environment variables.
 *
 * @see \Ghostwriter\Environment\Tests\Unit\EnvironmentTest
 */
final class Environment implements EnvironmentInterface
{
    /**
     * @var Collection<VariableInterface>
     */
    private Collection $variables;

    final public function __construct()
    {
        /** @var array<string,string> $_ENV */
        if ([] === $_ENV) {
            $_ENV = function_exists('getenv') ? (getenv() ?: []) : [];
        }

        if ([] === $_ENV) {
            $variablesOrder = ini_get('variables_order');
            if (false === $variablesOrder || ! str_contains($variablesOrder, 'E')) {
                throw new RuntimeException(
                    'Cannot get a list of the current environment variables. '
                    . 'Make sure the `variables_order` variable in php.ini '
                    . 'contains the letter "E". https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                );
            }
        }

        /** @var Collection<VariableInterface> $this->variables */
        $this->variables = Collection::fromGenerator(
            static function (): Generator {
                /** @var mixed|string $value */
                foreach ($_ENV as $name => $value) {
                    if (! is_string($name)) {
                        continue;
                    }
                    if (! is_string($value)) {
                        continue;
                    }
                    yield new EnvironmentVariable($name, $value);
                }
                /** @var mixed|string $value */
                foreach ($_SERVER as $name => $value) {
                    if (! is_string($name)) {
                        continue;
                    }
                    if (! is_string($value)) {
                        continue;
                    }
                    yield new ServerVariable($name, $value);
                }
            }
        );
    }

    public function count(): int
    {
        return $this->variables->count();
    }

    public function getEnvironmentVariable(string $name, ?string $default = null): string
    {
        $variable = $this->variables->first(
            static fn (VariableInterface $variable): bool =>
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
        ) ?? $default;

        if ($variable instanceof VariableInterface) {
            return $variable->getValue();
        }

        if (is_string($variable)) {
            return $variable;
        }

        throw new NotFoundException();
    }

    public function getEnvironmentVariables(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables->reduce(
            static fn (
                array $variables,
                VariableInterface $variable
            ): array =>
            $variable instanceof EnvironmentVariableInterface ?
                ($variables + $variable->asArray()) :
                $variables,
            []
        );
        return $variables;
    }

    public function getIterator(): Traversable
    {
        yield from $this->variables;
    }

    public function getServerVariable(string $name, ?string $default = null): string
    {
        $variable = $this->variables->first(
            static fn (VariableInterface $variable): bool =>
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
        ) ?? $default;

        if ($variable instanceof VariableInterface) {
            return $variable->getValue();
        }

        if (is_string($variable)) {
            return $variable;
        }

        throw new NotFoundException();
    }

    public function getServerVariables(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables->reduce(
            static fn (
                array $variables,
                VariableInterface $variable
            ): array =>
            $variable instanceof ServerVariableInterface ?
                ($variables + $variable->asArray()) :
                $variables,
            []
        );
        return $variables;
    }

    public function hasEnvironmentVariable(string $name): bool
    {
        return null !== $this->variables->first(
            static fn (
                VariableInterface $variable
            ): bool => $variable instanceof EnvironmentVariableInterface && $variable->getName() === $name
        );
    }

    public function hasServerVariable(string $name): bool
    {
        return null !== $this->variables->first(
            static fn (
                VariableInterface $variable
            ): bool => $variable instanceof ServerVariableInterface && $variable->getName() === $name
        );
    }

    public function setEnvironmentVariable(string $name, string $value): void
    {
        $this->mutate(
            static fn (VariableInterface $variable): bool =>
                ! (
                    $variable instanceof ServerVariableInterface && $variable->getName() === $name
                ),
            [new EnvironmentVariable($name, $value)]
        );
        $_ENV[$name] = $value;
    }

    public function setServerVariable(string $name, string $value): void
    {
        $this->mutate(
            static fn (VariableInterface $variable): bool =>
                ! (
                    $variable instanceof ServerVariableInterface && $variable->getName() === $name
                ),
            [new ServerVariable($name, $value)]
        );
        $_SERVER[$name] = $value;
    }

    public function toArray(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables->reduce(
            static fn (
                array $variables,
                VariableInterface $variable
            ): array => ($variables + $variable->asArray()),
            []
        );

        return $variables;
    }

    public function unsetEnvironmentVariable(string $name): void
    {
        if (! $this->hasEnvironmentVariable($name)) {
            throw new NotFoundException();
        }
        $this->mutate(
            static fn (VariableInterface $variable): bool =>
            ! ($variable instanceof EnvironmentVariableInterface && $variable->getName() === $name)
        );
        unset($_ENV[$name]);
    }

    public function unsetServerVariable(string $name): void
    {
        if (! $this->hasServerVariable($name)) {
            throw new NotFoundException();
        }
        $this->mutate(
            static fn (VariableInterface $variable): bool =>
            ! ($variable instanceof ServerVariableInterface && $variable->getName() === $name)
        );
        unset($_SERVER[$name]);
    }

    /**
     * @param Closure(VariableInterface):bool $mutation
     * @param array<VariableInterface>        $variables
     */
    private function mutate(Closure $mutation, array $variables = []): void
    {
        /** @var Collection<VariableInterface> $this->variables */
        $this->variables = $this->variables
            ->filter($mutation)
            ->append($variables);
    }
}
