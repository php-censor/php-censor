<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Plugin\Util;

use PHPCensor\Plugin\Util\FilesPluginInformation;

class FilesPluginInformationTest extends \PHPUnit_Framework_TestCase
{

    public function testGetInstalledPlugins_returnsObjects()
    {
        $pluginDirPath = dirname(dirname(dirname(dirname(__DIR__)))) . "/src/PHPCensor/Plugin/";
        $test          = FilesPluginInformation::newFromDir($pluginDirPath);
        $pluginInfos   = $test->getInstalledPlugins();

        $this->assertContainsOnlyInstancesOf('stdClass', $pluginInfos);
    }

    public function testGetPluginClasses_returnsStrings()
    {
        $pluginDirPath = dirname(dirname(dirname(dirname(__DIR__)))) . "/src/PHPCensor/Plugin";
        $test          = FilesPluginInformation::newFromDir($pluginDirPath);
        $pluginInfos   = $test->getPluginClasses();

        $this->assertContainsOnly('string', $pluginInfos);
    }
}

