<?php

namespace PHPCensor\Model\Build;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Model\Build;
use PHPCensor\StoreRegistry;

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
    public function __construct(
        protected ConfigurationInterface $configuration,
        StoreRegistry $storeRegistry,
        array $initialData = []
    ) {
        parent::__construct($storeRegistry, $initialData);
    }
}
