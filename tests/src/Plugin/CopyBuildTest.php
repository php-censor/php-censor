<?php

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Helper\CommandExecutor;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\CopyBuild;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CopyBuildTest extends TestCase
{
    protected $buildPath;

    protected $directories = [];

    protected function tearDown() : void
    {
        parent::tearDown();

        foreach ($this->directories as $directory) {
            if ($directory) {
                $filenames = [
                    'tree/four.php',
                    'tree',
                    '.two.yml',
                    'one.php',
                ];

                foreach ($filenames as $filename) {
                    if (\is_dir($directory . $filename)) {
                        @\rmdir($directory . $filename);
                    } elseif (\is_file($directory . $filename)) {
                        @\unlink($directory . $filename);
                    }
                }

                @\rmdir($directory);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $directory = $this->buildTemp();

        \mkdir($directory);

        \file_put_contents($directory . 'one.php', '');
        \file_put_contents($directory . '.two.yml', '');

        \mkdir($directory . 'tree');

        \file_put_contents($directory . 'tree/four.php', '');

        $this->directories[] = $directory;
        $this->buildPath     = $directory;
    }

    protected function buildTemp()
    {
        $directory = \tempnam(ROOT_DIR . 'tests/runtime/', 'copy_build_test_');
        @\unlink($directory);

        return $directory . '/';
    }

    protected function getPlugin(array $options = [])
    {
        /** @var MockObject|Build $build */
        $build = $this
            ->getMockBuilder('PHPCensor\Model\Build')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|Builder $builder */
        $builder = $this
            ->getMockBuilder('PHPCensor\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $buildLogger = $this
            ->getMockBuilder('PHPCensor\Logging\BuildLogger')
            ->disableOriginalConstructor()
            ->getMock();

        $executor = new CommandExecutor($buildLogger, '');

        $builder
            ->expects($this->any())
            ->method('interpolate')
            ->willReturnArgument(0);

        $builder
            ->expects($this->any())
            ->method('executeCommand')
            ->willReturnCallback(function () use ($executor) {
                $args = \func_get_args();

                return $executor->executeCommand($args);
            });

        $builder->buildPath = $this->buildPath;
        $builder->directory = $this->buildPath;

        return new CopyBuild($builder, $build, $options);
    }

    public function testExecuteAbsolute()
    {
        $directory           = $this->buildTemp();
        $this->directories[] = $directory;

        $plugin = $this->getPlugin([
            'directory' => $directory,
        ]);

        self::assertTrue($plugin->execute());

        self::assertTrue(\file_exists($directory));
        self::assertTrue(\is_dir($directory));

        self::assertTrue(\file_exists($directory . '/one.php'));
        self::assertTrue(\file_exists($directory . '.two.yml'));

        self::assertTrue(\file_exists($directory . '/tree'));
        self::assertTrue(\is_dir($directory . '/tree'));

        self::assertTrue(\file_exists($directory . '/tree/four.php'));
    }

    public function testExecuteRelative()
    {
        $directory           = '../copy_build_test_relative';
        $this->directories[] = ROOT_DIR . 'tests/runtime/copy_build_test_relative/';

        $plugin = $this->getPlugin([
            'directory' => $directory,
        ]);

        self::assertTrue($plugin->execute());

        self::assertTrue(\file_exists(\realpath($this->buildPath . '/../copy_build_test_relative')));
        self::assertTrue(\is_dir(\realpath($this->buildPath . '/../copy_build_test_relative')));

        self::assertTrue(\file_exists(\realpath($this->buildPath . '/../copy_build_test_relative/one.php')));
        self::assertTrue(\file_exists(\realpath($this->buildPath . '/../copy_build_test_relative/.two.yml')));

        self::assertTrue(\file_exists(\realpath($this->buildPath . '/../copy_build_test_relative/tree')));
        self::assertTrue(\is_dir(\realpath($this->buildPath . '/../copy_build_test_relative/tree')));

        self::assertTrue(\file_exists(\realpath($this->buildPath . '/../copy_build_test_relative/tree/four.php')));
    }
}
