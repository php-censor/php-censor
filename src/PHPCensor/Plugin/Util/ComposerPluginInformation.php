<?php

namespace PHPCensor\Plugin\Util;

use PHPCensor\Plugin;

/**
 * Class ComposerPluginInformation
 * 
 * @package PHPCensor\Plugin\Util
 */
class ComposerPluginInformation implements InstalledPluginInformation
{
    /**
     * @var array
     */
    protected $composerPackages;

    /**
     * @var array
     */
    protected $pluginInfo = null;

    /**
     * @param string $filePath The path of installed.json created by composer.
     * @return ComposerPluginInformation
     */
    public static function buildFromYaml($filePath)
    {
        if (file_exists($filePath)) {
            $installed = json_decode(file_get_contents($filePath));
        } else {
            $installed = [];
        }
        return new self($installed);
    }

    /**
     * Returns an array of objects. Each one represents an available plugin
     * and will have the following properties:
     *      name  - The friendly name of the plugin (may be an empty string)
     *      class - The class of the plugin (will include namespace)
     * @return \stdClass[]
     */
    public function getInstalledPlugins()
    {
        return $this->pluginInfo;
    }

    /**
     * Returns an array of all the class names of plugins that have been
     * loaded.
     *
     * @return string[]
     */
    public function getPluginClasses()
    {
        return array_map(
            function (Plugin $plugin) {
                return $plugin->class;
            },
            $this->getInstalledPlugins()
        );
    }

    /**
     * @param \stdClass[] $plugins
     * @param string $sourcePackageName
     * @param string $rootNamespace
     */
    protected function addPlugins(
        array $plugins,
        $sourcePackageName,
        $rootNamespace = ""
    ) {
        foreach ($plugins as $plugin) {
            if (!isset($plugin->class)) {
                continue;
            }
            $this->addPlugin($plugin, $sourcePackageName, $rootNamespace);
        }
    }

    /**
     * @param \stdClass $plugin
     * @param string $sourcePackageName
     * @param string $rootNamespace
     */
    protected function addPlugin(
        $plugin,
        $sourcePackageName,
        $rootNamespace = ""
    ) {
        $newPlugin = clone $plugin;

        $newPlugin->class = $rootNamespace . $newPlugin->class;

        if (!isset($newPlugin->name)) {
            $newPlugin->name = "";
        }

        $newPlugin->source = $sourcePackageName;

        $this->pluginInfo[] = $newPlugin;
    }
}
