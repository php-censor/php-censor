<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2013, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Plugin\Option\PhpUnitOptions;

/**
 * Unit test for the PHPUnitOptions parser
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitOptionsTest extends \PHPUnit_Framework_TestCase
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
                    'coverage' => '/path/to/coverage2/',
                    'args'     => [
                        'coverage-html' => '/path/to/coverage1/',
                    ],
                ],
                [
                    'coverage-html' => [
                        '/path/to/coverage1/',
                        '/path/to/coverage2/',
                    ],
                ],
            ],
            [
                [
                    'directory' => [
                        '/path/to/test1/',
                        '/path/to/test2/',
                    ],
                    'args'      => [
                        'coverage-html' => '/path/to/coverage1/',
                    ],
                ],
                [
                    'coverage-html' => '/path/to/coverage1/',
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
        $options = new PhpUnitOptions($rawOptions);
        $this->assertSame($parsedArguments, $options->getCommandArguments());
    }

    public function testGetters()
    {
        $options = new PhpUnitOptions(
            [
                'run_from' => '/path/to/run/from',
                'path'     => 'subTest',
            ]
        );

        $this->assertEquals('/path/to/run/from', $options->getRunFrom());
        $this->assertEquals('subTest', $options->getTestsPath());
        $this->assertNull($options->getOption('random'));
        $this->assertEmpty($options->getDirectories());
        $this->assertEmpty($options->getConfigFiles());

        $files = $options->getConfigFiles(ROOT_DIR);

        $this->assertFileExists(ROOT_DIR . $files[0]);
    }
}
