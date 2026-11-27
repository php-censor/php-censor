<?php

declare(strict_types=1);

namespace PHPCensor\Common\Repository;

use PHPCensor\Common\Environment\EnvironmentInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface EnvironmentRepositoryInterface
{
    public function getOneById(int $environmentId): ?EnvironmentInterface;
}
