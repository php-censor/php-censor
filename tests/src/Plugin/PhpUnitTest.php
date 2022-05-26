<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin;

use Monolog\Logger;
use PHPCensor\Builder;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\PhpUnit;
use PHPCensor\Store\BuildErrorStore;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitTest extends TestCase
{
    public function testSingleConfigFile(): void
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $plugin = $this
            ->getPluginBuilder($options)
            ->onlyMethods(['runConfig'])
            ->getMock();

        $plugin->expects($this->once())->method('runConfig')->with(null, ROOT_DIR . 'phpunit.xml.dist');

        $plugin->execute();
    }

    public function testMultiConfigFile(): void
    {
        $options = [
            'config' => [
                ROOT_DIR . 'phpunit1.xml',
                ROOT_DIR . 'phpunit2.xml',
            ]
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $plugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            [null, ROOT_DIR . 'phpunit1.xml'],
            [null, ROOT_DIR . 'phpunit2.xml']
        );

        $plugin->execute();
    }

    protected function getPluginBuilder(array $options = []): MockBuilder
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();

        $storeRegistry
            ->method('get')
            ->with('Build')
            ->willReturn($buildStore);

        $build = $this
            ->getMockBuilder(Build::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $build
            ->method('getId')
            ->willReturn(1);

        $build
            ->method('getProjectId')
            ->willReturn(1);

        $logger = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['Test'])
            ->onlyMethods(['addRecord'])
            ->getMock();
        $buildLogger = $this->getMockBuilder(BuildLogger::class)
            ->setConstructorArgs([$logger, $build])
            ->getMock();

        $buildErrorStore = $this
            ->getMockBuilder(BuildErrorStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();

        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();

        $secretStore = $this
            ->getMockBuilder(SecretStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();

        $environmentStore = $this
            ->getMockBuilder(EnvironmentStore::class)
            ->setConstructorArgs([$databaseManager, $storeRegistry])
            ->getMock();
        $builder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$configuration, $databaseManager, $storeRegistry, $buildErrorStore, $buildStore, $secretStore, $environmentStore, $build, $buildLogger])
            ->onlyMethods(['executeCommand'])
            ->getMock();

        return $this->getMockBuilder(PhpUnit::class)->setConstructorArgs(
            [$builder, $build, $options]
        );
    }

    public function testSingleDir(): void
    {
        $options = [
            'directories' => '/test/directory/one'
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $plugin->expects($this->once())->method('runConfig')->with('/test/directory/one', null);

        $plugin->execute();
    }

    public function testMultiDir(): void
    {
        $options = [
            'directories' => [
                '/test/directory/one',
                '/test/directory/two',
            ]
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $plugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            ['/test/directory/one'],
            ['/test/directory/two']
        );

        $plugin->execute();
    }

    public function testProcessResultsFromConfig(): void
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['processResults'])->getMock();
        $plugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $plugin->execute();
    }

    public function testProcessResultsFromDir(): void
    {
        $options = [
            'directories' => ROOT_DIR . 'Tests'
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['processResults'])->getMock();
        $plugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $plugin->execute();
    }

    public function testRequiredCoverageWithPassingPercentage(): void
    {
        $options = [
            'config'                  => ROOT_DIR . 'phpunit.xml.dist',
            'coverage'                => true,
            'required_lines_coverage' => 60,
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['extractCoverage', 'executePhpUnitCommand', 'processResults'])->getMock();
        $plugin->expects($this->once())->method('executePhpUnitCommand')->willReturn(true);
        $plugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '100.00',
            'methods' => '100.00',
            'lines'   => '100.00',
        ]);
        $this->assertTrue($plugin->execute());
    }

    public function testRequiredCoverageWithPassingPercentage2(): void
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist',
            'coverage' => true,
            'required_lines_coverage' => 60,
        ];

        $plugin = $this->getPluginBuilder($options)->onlyMethods(['extractCoverage', 'executePhpUnitCommand', 'processResults'])->getMock();
        $plugin->expects($this->once())->method('executePhpUnitCommand')->willReturn(false);
        $plugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '100.00',
            'methods' => '100.00',
            'lines'   => '100.00',
        ]);
        $this->assertFalse($plugin->execute());
    }

    public function testRequiredCoverageWithFailingPercentage(): void
    {
        $options = [
            'config'                  => ROOT_DIR . 'phpunit.xml.dist',
            'coverage'                => true,
            'required_lines_coverage' => 60,
        ];

        $plugin = $this->getPluginBuilder($options)
            ->onlyMethods(['extractCoverage', 'processResults'])
            ->getMock();
        $plugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '30.00',
            'methods' => '30.00',
            'lines'   => '30.00',
        ]);
        $this->assertFalse($plugin->execute());
    }
}
