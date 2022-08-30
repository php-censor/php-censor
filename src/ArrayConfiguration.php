<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\ParameterBag;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ArrayConfiguration extends ParameterBag implements ConfigurationInterface
{
    public function load(): void
    {
        return;
    }
}
