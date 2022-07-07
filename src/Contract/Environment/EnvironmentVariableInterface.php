<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Contract\Environment;

interface EnvironmentVariableInterface
{
    public function getName(): string;

    public function getValue(): string;
}
