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

        /** @var array<string,string> $environment */
        $environment = $_ENV;

        if ([] === $environment) {
            $environment = function_exists('getenv') ? getenv() ?: [] : [];
        }

        if ([] === $environment) {
            $variablesOrder = ini_get('variables_order');
            if (false === $variablesOrder || ! str_contains($variablesOrder, 'E')) {
                self::markTestSkipped(
                    'Cannot get a list of the current environment variables. '
                    . 'Make sure the `variables_order` variable in php.ini '
                    . 'contains the letter "E". https://www.php.net/manual/en/ini.core.php#ini.variables-order'
                );
            }
        }

        $this->backupEnvironmentVariables = $environment;

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
