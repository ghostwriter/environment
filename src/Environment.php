<?php

declare(strict_types=1);

namespace Ghostwriter\Environment;

use Ghostwriter\Environment\Contract\EnvironmentInterface;
use Ghostwriter\Environment\Contract\VariableInterface;
use Ghostwriter\Environment\Exception\InvalidNameException;
use Ghostwriter\Environment\Exception\InvalidValueException;
use Ghostwriter\Environment\Exception\NotFoundException;
use Ghostwriter\Environment\Exception\SetFailedException;
use Ghostwriter\Environment\Exception\UnsetFailedException;
use SplFixedArray;
use Traversable;
use function count;
use function getenv;
use function iterator_count;
use function putenv;
use function sprintf;

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
        $environment = getenv();

        /** @var SplFixedArray<VariableInterface> $this->variables */
        $this->variables = new SplFixedArray(count($environment));

        $index = 0;
        foreach ($environment as $name => $value) {
            $this->variables->offsetSet($index, new Variable($name, $value));
            ++$index;
        }
    }

    public function count(): int
    {
        return iterator_count($this);
    }

    public function getIterator(): Traversable
    {
        yield from $this->variables;
    }

    public function getVariable(string $name, ?string $default = null): string
    {
        foreach ($this as $variable) {
            if ($variable->getName() === $name) {
                return $variable->getValue();
            }
        }
        if (is_string($default)) {
            return $default;
        }
        throw new NotFoundException();
    }

    public function hasVariable(string $name): bool
    {
        foreach ($this as $variable) {
            if ($variable->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    /** @infection-ignore-all */
    public function setVariable(string $name, string $value): void
    {
        $setVariable = new Variable($name, $value);
        $hasBeenSet = putenv(sprintf('%s=%s', $name, $value));
        if (false === $hasBeenSet) {
            throw new SetFailedException();
        }
        foreach ($this as $index => $variable) {
            if ($variable->getName() === $name) {
                $this->variables->offsetSet($index, $setVariable);
                return;
            }
        }
        $index = $this->variables->count();
        $this->variables->setSize($index + 1);
        $this->variables->offsetSet($index, $setVariable);
    }

    public function toArray(): array
    {
        $variables = [];
        foreach ($this as $variable) {
            $variables[$variable->getName()] = $variable->getValue();
        }
        return $variables;
    }

    /** @infection-ignore-all */
    public function unsetVariable(string $name): void
    {
        $notFound = true;
        $index = 0;
        foreach ($this as $variable) {
            if ($variable->getName() === $name) {
                if (! putenv($name)) {
                    throw new UnsetFailedException();
                }
                $notFound = false;
                continue;
            }
            $this->variables->offsetSet($index, $variable);
            ++$index;
        }
        $this->variables->setSize($index);
        if (true === $notFound) {
            throw new NotFoundException();
        }
    }
}
