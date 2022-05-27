<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testEmptyYamlConfig(): void
    {
        $configuration = new Configuration('');

        self::assertEquals([], $configuration->all());
    }

    public function testYamlConfig(): void
    {
        $configuration = new Configuration(\dirname(__DIR__) . '/data/configuration.yml');

        self::assertEquals(['test' => 'test'], $configuration->all());
    }
}
