<?php

declare(strict_types=1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildErrorInterface
{
    public const SEVERITY_CRITICAL = 0;
    public const SEVERITY_HIGH     = 1;
    public const SEVERITY_NORMAL   = 2;
    public const SEVERITY_LOW      = 3;
}
