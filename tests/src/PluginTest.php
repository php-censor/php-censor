<?php

namespace Tests\PHPCensor;

use PHPCensor\Builder;
use PHPCensor\Helper\BuildInterpolator;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TestPlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return string[]
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * @return string
     */
    public function getBinaryPath()
    {
        return $this->binaryPath;
    }

    /**
     * @return array
     */
    public function getBinaryName()
    {
        return $this->binaryName;
    }
}

class PluginTest extends TestCase
{
    /**
     * @var MockObject|Builder
     */
    private $builder;

    /**
     * @var MockObject|Build
     */
    private $build;

    /**
     * @var string
     */
    private $currentDir;

    public function setUp()
    {
        $this->currentDir = rtrim(realpath(__DIR__ . '/../data/builds/build_x/'), '/\\') . '/';

        $this->builder = $this->createMock(Builder::class);
        $this->build   = $this->createMock(Build::class);

        $this->build
            ->method('getBuildPath')
            ->willReturn($this->currentDir);

        $this->build
            ->method('getCommitId')
            ->willReturn('commit_hash');

        $interpolator = new BuildInterpolator();
        $interpolator->setupInterpolationVars($this->build, 'http://php-censor.local/');

        $this->builder
            ->method('interpolate')
            ->willReturnCallback(function ($x) use ($interpolator) {
                return $interpolator->interpolate($x);
            });

        $this->builder->buildPath = $this->currentDir;
        $this->builder->directory = $this->currentDir;
    }

    public function testDirectory()
    {
        $plugin = new TestPlugin($this->builder, $this->build, []);
        self::assertEquals($this->currentDir, $plugin->getDirectory());

        $options = [
            'directory' => '',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir, $plugin->getDirectory());

        $options = [
            'directory' => 'relative',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'relative/', $plugin->getDirectory());

        $options = [
            'directory' => 'relative/',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'relative/', $plugin->getDirectory());

        $options = [
            'directory' => './relative',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'relative/', $plugin->getDirectory());

        $options = [
            'directory' => '../',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals(dirname(__DIR__) . '/data/builds/', $plugin->getDirectory());

        $options = [
            'directory' => '../../',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals(dirname(__DIR__) . '/data/', $plugin->getDirectory());

        $absoluteRealPath = rtrim(realpath(__DIR__ . '/../data/builds/build_x/'), '/\\') . '/';

        $options = [
            'directory' => $absoluteRealPath,
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($absoluteRealPath, $plugin->getDirectory());

        $options = [
            'directory' => '%BUILD_PATH%relative',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'relative/', $plugin->getDirectory());

        $options = [
            'directory' => '%BUILD_PATH%/relative/',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'relative/', $plugin->getDirectory());

        $options = [
            'directory' => '%COMMIT_ID%',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->currentDir . 'commit_hash/', $plugin->getDirectory());

        $options = [
            'directory' => $absoluteRealPath . '%COMMIT_ID%',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($absoluteRealPath . 'commit_hash/', $plugin->getDirectory());

        $this->builder->directory = $this->builder->interpolate($absoluteRealPath . '%COMMIT_ID%');

        $plugin = new TestPlugin($this->builder, $this->build, []);
        self::assertEquals($absoluteRealPath . 'commit_hash/', $plugin->getDirectory());

        $options = [
            'directory' => './docs',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($absoluteRealPath . 'docs/', $plugin->getDirectory());
    }

    public function testIgnore()
    {
        $this->builder->ignore = [];

        $options = [
            'ignore' => [],
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals([], $plugin->getIgnore());

        $this->builder->ignore = [
            './vendor',
            'tests'
        ];

        $options = [
            'ignore' => [],
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals([
            'vendor',
            'tests'
        ], $plugin->getIgnore());

        $this->builder->ignore = [
            './vendor',
            'tests'
        ];

        $options = [
            'ignore' => [
                '%BUILD_PATH%docs',
            ],
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals([
            'vendor',
            'tests',
            'docs',
        ], $plugin->getIgnore());
    }

    public function testBinary()
    {
        $this->builder->binaryPath = '/builder/bin/';

        $options = [
            'priority_path' => 'binary_path',
            'binary_path'   => '/option/%COMMIT_ID%/bin',
            'binary_name'   => 'example',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals('/option/commit_hash/bin/', $plugin->getBinaryPath());
        self::assertEquals(['example'], $plugin->getBinaryName());

        $options = [
            'priority_path' => 'binary_path',
            'binary_path'   => './bin2',
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals($this->builder->buildPath . 'bin2/', $plugin->getBinaryPath());

        $options = [
            'binary_name' => ['example1', 'example2'],
        ];

        $plugin = new TestPlugin($this->builder, $this->build, $options);
        self::assertEquals('/builder/bin/', $plugin->getBinaryPath());
        self::assertEquals(['example1', 'example2'], $plugin->getBinaryName());

        $absoluteRealPath          = rtrim(realpath(__DIR__ . '/../data/builds/build_x/'), '/\\') . '/';
        $this->builder->binaryPath = $this->builder->interpolate($absoluteRealPath . '%COMMIT_ID%/');

        $plugin = new TestPlugin($this->builder, $this->build, []);
        self::assertEquals($absoluteRealPath . 'commit_hash/', $plugin->getBinaryPath());
    }
}
