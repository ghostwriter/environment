<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\EnvironmentInterface;
use Ghostwriter\Environment\Contract\EnvironmentVariableInterface;
use Ghostwriter\Environment\Contract\ServerVariableInterface;
use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\NotFoundException;
use RuntimeException;
use SplFixedArray;
use Traversable;

/**
 * Maps environment variables.
 *
 * @see \Ghostwriter\Environment\Tests\Unit\EnvironmentTest
 */
final class Environment implements EnvironmentInterface
{
    /**
     * @var SplFixedArray<VariableInterface>
     */
    private SplFixedArray $variables;

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

        $index = 0;

        $environmentVariables = $this->filterStringNameAndValue($_ENV);

        /** @var SplFixedArray<VariableInterface> $this->variables */
        $this->variables = new SplFixedArray(count($environmentVariables));

        foreach ($environmentVariables as $name => $value) {
            $this->variables->offsetSet($index++, new EnvironmentVariable($name, $value));
        }

        $serverVariables = $this->filterStringNameAndValue($_SERVER);

        $this->variables->setSize($index + count($serverVariables));

        foreach ($serverVariables as $name => $value) {
            $this->variables->offsetSet($index++, new ServerVariable($name, $value));
        }
    }

    public function count(): int
    {
        return iterator_count($this->filterType(VariableInterface::class));
    }

    public function getEnvironmentVariable(string $name, ?string $default = null): string
    {
        foreach ($this->findVariable($name, EnvironmentVariableInterface::class) as $variable) {
            return $variable->getValue();
        }

        if (null === $default) {
            throw new NotFoundException();
        }

        return $default;
    }

    public function getEnvironmentVariables(): array
    {
        $variables = [];
        foreach ($this->filterType(EnvironmentVariableInterface::class) as $variable) {
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    public function getIterator(): Traversable
    {
        yield from $this->variables;
//        /** @var VariableInterface $variable */
//        foreach (new CallbackFilterIterator(
//                     $this->variables->getIterator(),
//            static fn ($current) => $current instanceof VariableInterface
//        ) as $variable) {
//            yield $variable;
//        }
    }

    public function getServerVariable(string $name, ?string $default = null): string
    {
        foreach ($this->findVariable($name, ServerVariableInterface::class) as $variable) {
            return $variable->getValue();
        }

        if (null === $default) {
            throw new NotFoundException();
        }

        return $default;
    }

    public function getServerVariables(): array
    {
        $variables = [];
        foreach ($this->filterType(ServerVariableInterface::class) as $variable) {
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    public function hasEnvironmentVariable(string $name): bool
    {
        return iterator_count($this->findVariable($name, EnvironmentVariableInterface::class)) > 0;
    }

    public function hasServerVariable(string $name): bool
    {
        return iterator_count($this->findVariable($name, ServerVariableInterface::class)) > 0;
    }

    public function setEnvironmentVariable(string $name, string $value): void
    {
        $environmentVariable = new EnvironmentVariable($name, $value);
        if (! iterator_count($this->removeVariable($name, EnvironmentVariableInterface::class))) {
            $_ENV[$name] = $value;
            $index = $this->variables->count();
            $this->variables->setSize($index + 1);
            $this->variables->offsetSet($index, $environmentVariable);
            return;
        }
        foreach ($this->variables as $index => $variable) {
            if (! $variable instanceof EnvironmentVariableInterface) {
                continue;
            }
            if ($variable->getName() !== $name) {
                continue;
            }
            $_ENV[$name] = $value;
            $this->variables->offsetSet($index, $environmentVariable);
            return;
        }
        foreach ($this->variables as $index => $variable) {
            if ($variable instanceof VariableInterface) {
                continue;
            }
            $_ENV[$name] = $value;
            $this->variables->offsetSet($index, $environmentVariable);
            return;
        }
        $_ENV[$name] = $value;
        $index = $this->variables->count();
        $this->variables->setSize($index + 1);
        $this->variables->offsetSet($index, $environmentVariable);
    }

    public function setServerVariable(string $name, string $value): void
    {
        $serverVariable = new ServerVariable($name, $value);
        if (! iterator_count($this->removeVariable($name, ServerVariableInterface::class))) {
            $_SERVER[$name] = $value;
            $index = $this->variables->count();
            $this->variables->setSize($index + 1);
            $this->variables->offsetSet($index, $serverVariable);
            return;
        }
        foreach ($this->variables as $index => $variable) {
            if ($variable instanceof VariableInterface) {
                continue;
            }
            $_SERVER[$name] = $value;
            $this->variables->offsetSet($index, $serverVariable);
            return;
        }
    }

    public function toArray(): array
    {
        $variables = [];
        foreach ($this->filterType(VariableInterface::class) as $variable) {
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    public function unsetEnvironmentVariable(string $name): void
    {
        if (! iterator_count($this->removeVariable($name, EnvironmentVariableInterface::class))) {
            throw new NotFoundException();
        }
        unset($_ENV[$name]);
    }

    public function unsetServerVariable(string $name): void
    {
        if (! iterator_count($this->removeVariable($name, ServerVariableInterface::class))) {
            throw new NotFoundException();
        }
        unset($_SERVER[$name]);
    }

    /**
     * @return array<string,string>
     */
    private function filterStringNameAndValue(array $input): array
    {
        /**
         * @var array<string,string>
         */
        return array_filter(
            $input,
            static fn ($value, $name): bool => is_string($name) && is_string($value),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @param class-string<VariableInterface> $type
     *
     * @return Traversable<int,VariableInterface>
     */
    private function filterType(string $type): iterable
    {
        foreach ($this->variables as $variable) {
            if (! is_a($variable, $type)) {
                continue;
            }
            yield $variable;
        }
    }

    /**
     * @param class-string<VariableInterface> $type
     *
     * @return Traversable<int,VariableInterface>
     */
    private function findVariable(string $name, string $type): iterable
    {
        foreach ($this->variables as $variable) {
            if (! is_a($variable, $type)) {
                continue;
            }
            if ($variable->getName() !== $name) {
                continue;
            }
            yield $variable;
        }
    }

    /**
     * @param class-string<VariableInterface> $type
     *
     * @return Traversable<int,VariableInterface>
     */
    private function removeVariable(string $name, string $type): iterable
    {
        foreach ($this->variables as $index => $variable) {
            if (! is_a($variable, $type)) {
                continue;
            }
            if ($variable->getName() !== $name) {
                continue;
            }
            $this->variables->offsetUnset($index);
            yield $variable;
        }
    }
}
