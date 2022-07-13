<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract;

interface VariableInterface
{
    /**
     * Get the variable as an array ['name'=>'value'].
     *
     * @return array<string,string>
     */
    public function asArray(): array;

    /**
     * Get the variable name.
     */
    public function getName(): string;

    /**
     * Get the variable value.
     */
    public function getValue(): string;
}
