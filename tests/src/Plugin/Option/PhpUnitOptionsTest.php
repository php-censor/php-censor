<?php

namespace Tests\PHPCensor\Plugin\Option;

use PHPCensor\Builder;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Plugin\Option\PhpUnitOptions;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the PHPUnitOptions parser
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitOptionsTest extends TestCase
{
    public function validOptionsProvider()
    {
        return [
            [
                [
                    'config' => 'tests/phpunit.xml',
                    'args'   => '--stop-on-error --log-junit /path/to/log/',
                ],
                [
                    'stop-on-error' => '',
                    'log-junit'     => '/path/to/log/',
                    'configuration' => 'tests/phpunit.xml',
                ],
            ],
            [
                [
                    'coverage' => '',
                ],
                [],
            ],
            [
                [
                    'coverage' => '/path/to/coverage2/',
                ],
                [
                    'coverage-text' => null,
                ],
            ],
            [
                [
                    'coverage' => true,
                    'directory' => [
                        '/path/to/test1/',
                        '/path/to/test2/',
                    ],
                ],
                [
                    'coverage-text' => null,
                ],
            ],
            [
                [
                    'config' => ['tests/phpunit.xml'],
                    'args'   => "--testsuite=unit --bootstrap=vendor/autoload.php",
                ],
                [
                    'testsuite'     => 'unit',
                    'bootstrap'     => 'vendor/autoload.php',
                    'configuration' => ['tests/phpunit.xml'],
                ],
            ],
            [
                [
                    'config' => ['tests/phpunit.xml'],
                    'args'   => "--testsuite='unit' --bootstrap 'vendor/autoload.php'",
                ],
                [
                    'testsuite'     => 'unit',
                    'bootstrap'     => 'vendor/autoload.php',
                    'configuration' => ['tests/phpunit.xml'],
                ],
            ],
            [
                [
                    'config' => ['tests/phpunit.xml'],
                    'args'   => '--testsuite="unit" --bootstrap "vendor/autoload.php"',
                ],
                [
                    'testsuite'     => 'unit',
                    'bootstrap'     => 'vendor/autoload.php',
                    'configuration' => ['tests/phpunit.xml'],
                ],
            ],
        ];
    }

    /**
     * @param $rawOptions
     * @param $parsedArguments
     *
     * @dataProvider validOptionsProvider
     */
    public function testCommandArguments($rawOptions, $parsedArguments)
    {
        $configuration = $this->createMock(ConfigurationInterface::class);

        $options = new PhpUnitOptions($configuration, $rawOptions, '/location');
        self::assertSame($parsedArguments, $options->getCommandArguments());
    }

    public function testGetters()
    {
        $configuration = $this->createMock(ConfigurationInterface::class);

        $options = new PhpUnitOptions(
            $configuration,
            [
                'run_from' => '/path/to/run/from',
                'path'     => 'subTest',
            ],
            '/location'
        );

        $builder = $this->createMock(Builder::class);

        self::assertEquals('/path/to/run/from', $options->getRunFrom());
        self::assertEquals('subTest', $options->getTestsPath());
        self::assertNull($options->getOption('random'));
        self::assertEmpty($options->getDirectories());
        self::assertEmpty($options->getConfigFiles());

        $files = $options->getConfigFiles(ROOT_DIR);

        self::assertFileExists(ROOT_DIR . $files[0]);
    }
}
