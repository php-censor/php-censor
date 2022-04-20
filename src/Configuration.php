<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\ParameterBag;
use Symfony\Component\Yaml\Parser as YamlParser;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Configuration extends ParameterBag implements ConfigurationInterface
{
    private string $configurationPath;

    public function __construct(string $configurationPath)
    {
        parent::__construct([]);

        $this->configurationPath = $configurationPath;

        $this->load();
    }

    public function load()
    {
        $parameters = [];
        if ($this->configurationPath && \file_exists($this->configurationPath)) {
            $parameters = $this->loadYaml($this->configurationPath);
        }

        $this->parameters = $parameters;
    }

    private function loadYaml(string $configurationPath): array
    {
        $parser = new YamlParser();
        $yaml   = \file_get_contents($configurationPath);

        return (array)$parser->parse($yaml);
    }
}
