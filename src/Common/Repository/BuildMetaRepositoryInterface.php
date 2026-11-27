<?php

declare(strict_types=1);

namespace PHPCensor\Common\Repository;

use PHPCensor\Common\Build\BuildMetaInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaRepositoryInterface
{
    public function getOneByBuildIdAndPluginAndKey(
        int $buildId,
        string $plugin,
        string $key
    ): ?BuildMetaInterface;
}
