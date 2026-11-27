<?php

declare(strict_types=1);

namespace PHPCensor\Common\Repository;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildErrorRepositoryInterface
{
    public function getErrorsCountPerPluginByBuildId(int $buildId): array;
}
