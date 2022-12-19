<?php

declare(strict_types=1);

namespace PHPCensor\Model\Build;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Model\Build;

/**
 * Remote Typed Build Model
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class TypedBuild extends Build
{
    protected ConfigurationInterface $configuration;

    public function __construct(
        ConfigurationInterface $configuration,
        array $initialData = []
    ) {
        parent::__construct($initialData);

        $this->configuration = $configuration;
    }
}
