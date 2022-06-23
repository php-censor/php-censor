<?php

declare(strict_types=1);

namespace PHPCensor\Model;

use PHPCensor\Common\Build\BuildMetaInterface;
use PHPCensor\Model\Base\BuildMeta as BaseBuildMeta;
use PHPCensor\Store\BuildStore;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildMeta extends BaseBuildMeta implements BuildMetaInterface
{
    public function getBuild(): ?Build
    {
        $buildId = $this->getBuildId();
        if (empty($buildId)) {
            return null;
        }

        /** @var BuildStore $buildStore */
        $buildStore = $this->storeRegistry->get('Build');

        return $buildStore->getById($buildId);
    }

    public function getKey(): ?string
    {
        $metaKey  = $this->getMetaKey();

        return \substr($metaKey, 0, \strpos($metaKey, '-'));
    }

    public function getValue()
    {
        return $this->getMetaValue();
    }

    public function getPlugin(): ?string
    {
        $metaKey  = $this->getMetaKey();

        return \substr($metaKey, (\strpos($metaKey, '-') + 1));
    }
}
