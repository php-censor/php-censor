<?php

declare(strict_types = 1);

namespace PHPCensor;

use PHPCensor\Common\ParameterBag;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Configuration extends ParameterBag implements ConfigurationInterface
{
    /**
     * @param string $configurationPath
     */
    public function __construct(string $configurationPath)
    {
        $parameters = [];
        if ($configurationPath && \file_exists($configurationPath)) {
            $parameters = $this->loadYaml($configurationPath);
        }

        parent::__construct($parameters);
    }

    /**
     * @param string $configurationPath
     *
     * @return array
     */
    private function loadYaml(string $configurationPath): array
    {
        $parser = new YamlParser();
        $yaml   = \file_get_contents($configurationPath);

        return (array)$parser->parse($yaml);
    }
}
