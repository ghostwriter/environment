<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int,VariableInterface>
 * @extends Countable
 */
interface EnvironmentInterface extends Countable, EnvironmentVariablesInterface, IteratorAggregate, ServerVariablesInterface
{
    /**
     * Get the number of environment variables.
     */
    public function count(): int;

    /**
     * Get all environment variables.
     *
     * @return Traversable<int,VariableInterface>
     */
    public function getIterator(): Traversable;

    /**
     * Get an array copy of all the environment variables.
     *
     * @return array<string,string>
     */
    public function toArray(): array;
}
