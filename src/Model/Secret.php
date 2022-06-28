<?php

declare(strict_types=1);

namespace PHPCensor\Model;

use PHPCensor\Common\Secret\SecretInterface;
use PHPCensor\Model\Base\Secret as BaseSecret;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Secret extends BaseSecret implements SecretInterface
{
}
