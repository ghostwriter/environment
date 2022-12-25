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

    public function __construct()
    {
        /** @var Collection<VariableInterface> $this->variables */
        $this->variables = Collection::fromGenerator(
            static function (): Generator {
                /** @var array<string,string> $_ENV */
                $_ENV = ([] === $_ENV && function_exists('getenv')) ? getenv() : $_ENV;

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

                /**
                 * @var mixed|string $name
                 * @var mixed|string $value
                 */
                foreach ($_ENV as $name => $value) {
                    if (! is_string($name)) {
                        continue;
                    }

                    if (! is_string($value)) {
                        continue;
                    }

                    yield new EnvironmentVariable($name, $value);
                }

                /**
                 * @var mixed|string $name
                 * @var mixed|string $value
                 */
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
        /** @var ?EnvironmentVariableInterface $variable */
        $variable = $this->variables->first(
            static fn (VariableInterface $variable, int $_): bool =>
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
        );

        if ($variable instanceof VariableInterface) {
            return $variable->getValue();
        }

        if (null === $default) {
            throw new NotFoundException();
        }

        return $default;
    }

    public function getEnvironmentVariables(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables
            ->filter(
                static fn (
                    VariableInterface $variable,
                    int $_
                ): bool => $variable instanceof EnvironmentVariableInterface
            )
            ->map(static fn (VariableInterface $variable, int $_): array => $variable->toArray())
            ->reduce(
                static fn (?array $variables, array $variable, int $_): array => ($variables ?? []) + $variable,
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
        /** @var ?ServerVariableInterface $variable */
        $variable = $this->variables->first(
            static fn (VariableInterface $variable, int $_): bool =>
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
        );

        if ($variable instanceof VariableInterface) {
            return $variable->getValue();
        }

        if (null === $default) {
            throw new NotFoundException();
        }

        return $default;
    }

    public function getServerVariables(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables
            ->filter(
                static fn (
                    VariableInterface $variable,
                    int $_
                ): bool => $variable instanceof ServerVariableInterface
            )
            ->map(static fn (VariableInterface $variable, int $_): array => $variable->toArray())
            ->reduce(
                static fn (
                    mixed $variables,
                    array $variable,
                    int $_
                ): array => is_array($variables) ? $variables + $variable : $variable,
                []
            );
        return $variables;
    }

    public function hasEnvironmentVariable(string $name): bool
    {
        return null !== $this->variables->first(
            static fn (
                VariableInterface $variable,
                int $_
            ): bool => $variable instanceof EnvironmentVariableInterface && $variable->getName() === $name
        );
    }

    public function hasServerVariable(string $name): bool
    {
        return null !== $this->variables->first(
            static fn (
                VariableInterface $variable,
                int $_
            ): bool => $variable instanceof ServerVariableInterface && $variable->getName() === $name
        );
    }

    public function setEnvironmentVariable(string $name, string $value): void
    {
        $this->mutate(
            static fn (
                VariableInterface $variable,
                int $_
            ): bool => ! ($variable instanceof EnvironmentVariableInterface && $variable->getName() === $name),
            [new EnvironmentVariable($name, $value)]
        );
        $_ENV[$name] = $value;
    }

    public function setServerVariable(string $name, string $value): void
    {
        $this->mutate(
            static fn (
                VariableInterface $variable,
                int $_
            ): bool => ! ($variable instanceof ServerVariableInterface && $variable->getName() === $name),
            [new ServerVariable($name, $value)]
        );
        $_SERVER[$name] = $value;
    }

    public function toArray(): array
    {
        /** @var array<string,string> $variables */
        $variables = $this->variables
            ->map(static fn (VariableInterface $variable, int $_): array => $variable->toArray())
            ->reduce(
                static fn (?array $variables, array $variable, int $_): array => ($variables ?? []) + $variable,
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
        /** @var non-empty-string $name */
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

        /** @var non-empty-string $name */
        unset($_SERVER[$name]);
    }

    /**
     * @param Closure(VariableInterface,int):bool $mutation
     * @param array<VariableInterface>            $variables
     */
    private function mutate(Closure $mutation, array $variables = []): void
    {
        /** @var Collection<VariableInterface> $this->variables */
        $this->variables = $this->variables
            ->filter($mutation)
            ->append($variables);
    }
}
