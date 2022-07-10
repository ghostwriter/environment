<?php

declare(strict_types=1);

namespace Ghostwriter\Environment\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @var array<string,string>
     */
    protected array $backupEnvironmentVariables = [];

    /**
     * @var array<string,string>
     */
    protected array $backupServerVariables = [];

    protected function setUp(): void
    {
        parent::setUp();
        /** @var array<string,string> $this->backupEnvironmentVariables */
        $this->backupEnvironmentVariables = getenv() ?: $_ENV;
        /** @var array<string,string> $this->backupServerVariables */
        $this->backupServerVariables = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->backupEnvironmentVariables;
        $_SERVER = $this->backupServerVariables;
        parent::tearDown();
    }
}
