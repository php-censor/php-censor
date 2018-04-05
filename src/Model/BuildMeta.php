<?php

namespace PHPCensor\Model;

use PHPCensor\Model\Base\BuildMeta as BaseBuildMeta;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\Factory;

class BuildMeta extends BaseBuildMeta
{
    /**
     * @return Build|null
     */
    public function getBuild()
    {
        $buildId = $this->getBuildId();
        if (empty($buildId)) {
            return null;
        }

        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        return $buildStore->getById($buildId);
    }
}
