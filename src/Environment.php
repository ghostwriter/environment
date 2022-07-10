<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\EnvironmentInterface;
use Ghostwriter\Environment\Contract\EnvironmentVariableInterface;
use Ghostwriter\Environment\Contract\ServerVariableInterface;
use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use RuntimeException;
use SplFixedArray;
use Traversable;
use function count;
use function iterator_count;

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

    /**
     * @throws InvalidNameException
     * @throws InvalidValueException
     */
    public function __construct()
    {
        $environment = function_exists('getenv') ? (getenv() ?: $_ENV) : $_ENV;
        if ([] === $environment) {
            $variablesOrder = ini_get('variables_order');
            if (false === $variablesOrder || ! str_contains($variablesOrder, 'E')) {
                throw new RuntimeException(
                    'Cannot get a list of the current environment variables. '
                    . 'Make sure the `variables_order` variable in php.ini '
                    . 'contains the letter "E". https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                );
            }
        }

        $environmentVariables = $this->filterStringNameAndValue($_ENV);
        $serverVariables = $this->filterStringNameAndValue($_SERVER);

        /** @var SplFixedArray<VariableInterface> $this->variables */
        $this->variables = new SplFixedArray(count($environmentVariables) + count($serverVariables));

        $index = 0;
        foreach ($environmentVariables as $name => $value) {
            $this->variables->offsetSet($index++, new EnvironmentVariable($name, $value));
        }

        foreach ($serverVariables as $name => $value) {
            $this->variables->offsetSet($index++, new ServerVariable($name, $value));
        }
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    public function getEnvironmentVariable(string $name, ?string $default = null): string
    {
        foreach ($this as $variable) {
            if (
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
            ) {
                return $variable->getValue();
            }
        }
        if (is_string($default)) {
            return $default;
        }
        throw new NotFoundException();
    }

    public function getIterator(): Traversable
    {
        yield from $this->variables;
    }

    public function getServerVariable(string $name, ?string $default = null): string
    {
        foreach ($this as $variable) {
            if (
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
            ) {
                return $variable->getValue();
            }
        }
        if (is_string($default)) {
            return $default;
        }
        throw new NotFoundException();
    }

    public function hasEnvironmentVariable(string $name): bool
    {
        foreach ($this as $variable) {
            if (
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
            ) {
                return true;
            }
        }
        return false;
    }

    public function hasServerVariable(string $name): bool
    {
        foreach ($this as $variable) {
            if (
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
            ) {
                return true;
            }
        }
        return false;
    }

    public function setEnvironmentVariable(string $name, string $value): void
    {
        $environmentVariable = new EnvironmentVariable($name, $value);
        foreach ($this as $index => $variable) {
            if (
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
            ) {
                $this->variables->offsetSet($index, $environmentVariable);
                $_ENV[$name] = $value;
                return;
            }
        }
        $index = $this->variables->count();
        $this->variables->setSize($index + 1);
        $this->variables->offsetSet($index, $environmentVariable);
        $_ENV[$name] = $value;
    }

    public function setServerVariable(string $name, string $value): void
    {
        $serverVariable = new ServerVariable($name, $value);
        foreach ($this as $index => $variable) {
            if (
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
            ) {
                $this->variables->offsetSet($index, $serverVariable);
                $_SERVER[$name] = $value;
                return;
            }
        }
        $index = $this->variables->count();
        $this->variables->setSize($index + 1);
        $this->variables->offsetSet($index, $serverVariable);
        $_SERVER[$name] = $value;
    }

    public function toArray(): array
    {
        $variables = [];
        foreach ($this as $variable) {
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    public function unsetEnvironmentVariable(string $name): void
    {
        $notFound = true;
        $index = 0;
        foreach ($this as $variable) {
            if (
                $variable instanceof EnvironmentVariableInterface &&
                $variable->getName() === $name
            ) {
                unset($_ENV[$name]);
                $notFound = false;
                continue;
            }
            $this->variables->offsetSet($index++, $variable);
        }
        $this->variables->setSize($index);
        if (true === $notFound) {
            throw new NotFoundException();
        }
    }

    public function unsetServerVariable(string $name): void
    {
        $notFound = true;
        $index = 0;
        foreach ($this as $variable) {
            if (
                $variable instanceof ServerVariableInterface &&
                $variable->getName() === $name
            ) {
                unset($_SERVER[$name]);
                $notFound = false;
                continue;
            }
            $this->variables->offsetSet($index++, $variable);
        }
        $this->variables->setSize($index);
        if (true === $notFound) {
            throw new NotFoundException();
        }
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
}
