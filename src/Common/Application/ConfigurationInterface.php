<?php

declare(strict_types=1);

namespace PHPCensor\Common\Application;

use PHPCensor\Common\ParameterBagInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ConfigurationInterface extends ParameterBagInterface
{
    public function load(): void;
}
