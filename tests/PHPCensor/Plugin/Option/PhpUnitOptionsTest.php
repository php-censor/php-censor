<?php

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Plugin\Option\PhpUnitOptions;

/**
 * Unit test for the PHPUnitOptions parser
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitOptionsTest extends \PHPUnit\Framework\TestCase
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
                    'coverage-html' => '/location',
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
                    'coverage-html' => '/location',
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
        $options = new PhpUnitOptions($rawOptions, '/location');
        self::assertSame($parsedArguments, $options->getCommandArguments());
    }

    public function testGetters()
    {
        $options = new PhpUnitOptions(
            [
                'run_from' => '/path/to/run/from',
                'path'     => 'subTest',
            ],
            '/location'
        );

        self::assertEquals('/path/to/run/from', $options->getRunFrom());
        self::assertEquals('subTest', $options->getTestsPath());
        self::assertNull($options->getOption('random'));
        self::assertEmpty($options->getDirectories());
        self::assertEmpty($options->getConfigFiles());

        $files = $options->getConfigFiles(ROOT_DIR);

        self::assertFileExists(ROOT_DIR . $files[0]);
    }
}
