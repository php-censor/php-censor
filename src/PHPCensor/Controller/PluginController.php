<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Controller;

use PHPCensor\Helper\Lang;
use PHPCensor\Plugin\Util\ComposerPluginInformation;
use PHPCensor\Plugin\Util\FilesPluginInformation;
use PHPCensor\Plugin\Util\PluginInformationCollection;
use PHPCensor\Controller;

/**
 * Plugin Controller - Provides support for installing Composer packages.
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Web
 */
class PluginController extends Controller
{
    /**
     * List all enabled plugins, installed and recommend packages.
     * @return string
     */
    public function index()
    {
        $this->requireAdmin();

        $json = $this->getComposerJson();
        $this->view->installedPackages = $json['require'];

        $pluginInfo = new PluginInformationCollection();
        $pluginInfo->add(FilesPluginInformation::newFromDir(SRC_DIR . "Plugin" . DIRECTORY_SEPARATOR));
        $pluginInfo->add(ComposerPluginInformation::buildFromYaml(
            ROOT_DIR . "vendor" . DIRECTORY_SEPARATOR . "composer" . DIRECTORY_SEPARATOR . "installed.json"
        ));

        $this->view->plugins = $pluginInfo->getInstalledPlugins();

        $this->layout->title = Lang::get('plugins');

        return $this->view->render();
    }

    /**
     * Get the json-decoded contents of the composer.json file.
     * @return mixed
     */
    protected function getComposerJson()
    {
        $json = file_get_contents(ROOT_DIR . 'composer.json');
        return json_decode($json, true);
    }
}
