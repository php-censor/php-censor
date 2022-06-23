<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildMetaWriterInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildMetaWriter implements BuildMetaWriterInterface
{
    public function write(
        BuildInterface $build,
        ?string $plugin,
        string $key,
        $value
    ): void {
        $build->storeMeta(\sprintf('%-%', $plugin, $key), $value);
    }
}
