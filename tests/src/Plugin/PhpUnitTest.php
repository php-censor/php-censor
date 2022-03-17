<?php

namespace Tests\PHPCensor\Plugin;

use Monolog\Logger;
use PHPCensor\Builder;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Plugin\PhpUnit;
use PHPCensor\Store\BuildStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author Pablo Tejada <pablo@ptejada.com>
 */
class PhpUnitTest extends TestCase
{
    public function testSingleConfigFile()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $mockPlugin = $this
            ->getPluginBuilder($options)
            ->onlyMethods(['runConfig'])
            ->getMock();

        $mockPlugin->expects($this->once())->method('runConfig')->with(null, ROOT_DIR . 'phpunit.xml.dist');

        $mockPlugin->execute();
    }

    public function testMultiConfigFile()
    {
        $options = [
            'config' => [
                ROOT_DIR . 'phpunit1.xml',
                ROOT_DIR . 'phpunit2.xml',
            ]
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            [null, ROOT_DIR . 'phpunit1.xml'],
            [null, ROOT_DIR . 'phpunit2.xml']
        );

        $mockPlugin->execute();
    }



    /**
     * @param array $options
     *
     */
    protected function getPluginBuilder($options = [])
    {
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setConstructorArgs(['Test'])
            ->onlyMethods(['addRecord'])
            ->getMock();

        $mockConfiguration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $mockDatabaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$mockConfiguration])
            ->getMock();
        $storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$mockDatabaseManager])
            ->getMock();

        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$mockDatabaseManager, $storeRegistry])
            ->getMock();

        $storeRegistry
            ->method('get')
            ->with('Build')
            ->willReturn($buildStore);

        $mockBuild = $this
            ->getMockBuilder(Build::class)
            ->setConstructorArgs([$storeRegistry])
            ->getMock();

        $mockBuild
            ->method('getId')
            ->willReturn(1);

        $mockBuild
            ->method('getProjectId')
            ->willReturn(1);

        $mockBuilder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$mockConfiguration, $mockDatabaseManager, $storeRegistry, $mockBuild, $loggerMock])
            ->onlyMethods(['executeCommand'])
            ->getMock();

        return $this->getMockBuilder(PhpUnit::class)->setConstructorArgs(
            [$mockBuilder, $mockBuild, $options]
        );
    }

    public function testSingleDir()
    {
        $options = [
            'directories' => '/test/directory/one'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->once())->method('runConfig')->with('/test/directory/one', null);

        $mockPlugin->execute();
    }

    public function testMultiDir()
    {
        $options = [
            'directories' => [
                '/test/directory/one',
                '/test/directory/two',
            ]
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['runConfig'])->getMock();
        $mockPlugin->expects($this->exactly(2))->method('runConfig')->withConsecutive(
            ['/test/directory/one'],
            ['/test/directory/two']
        );

        $mockPlugin->execute();
    }

    public function testProcessResultsFromConfig()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testProcessResultsFromDir()
    {
        $options = [
            'directories' => ROOT_DIR . 'Tests'
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('processResults')->with($this->isType('string'));

        $mockPlugin->execute();
    }

    public function testRequiredCoverageWithPassingPercentage()
    {
        $options = [
            'config'                  => ROOT_DIR . 'phpunit.xml.dist',
            'coverage'                => true,
            'required_lines_coverage' => 60,
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['extractCoverage', 'executePhpUnitCommand', 'processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('executePhpUnitCommand')->willReturn(true);
        $mockPlugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '100.00',
            'methods' => '100.00',
            'lines'   => '100.00',
        ]);
        $this->assertTrue($mockPlugin->execute());
    }

    public function testRequiredCoverageWithPassingPercentage2()
    {
        $options = [
            'config' => ROOT_DIR . 'phpunit.xml.dist',
            'coverage' => true,
            'required_lines_coverage' => 60,
        ];

        $mockPlugin = $this->getPluginBuilder($options)->onlyMethods(['extractCoverage', 'executePhpUnitCommand', 'processResults'])->getMock();
        $mockPlugin->expects($this->once())->method('executePhpUnitCommand')->willReturn(false);
        $mockPlugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '100.00',
            'methods' => '100.00',
            'lines'   => '100.00',
        ]);
        $this->assertFalse($mockPlugin->execute());
    }

    public function testRequiredCoverageWithFailingPercentage()
    {
        $options = [
            'config'                  => ROOT_DIR . 'phpunit.xml.dist',
            'coverage'                => true,
            'required_lines_coverage' => 60,
        ];

        $mockPlugin = $this->getPluginBuilder($options)
            ->onlyMethods(['extractCoverage', 'processResults'])
            ->getMock();
        $mockPlugin->expects($this->once())->method('extractCoverage')->willReturn([
            'classes' => '30.00',
            'methods' => '30.00',
            'lines'   => '30.00',
        ]);
        $this->assertFalse($mockPlugin->execute());
    }
}
