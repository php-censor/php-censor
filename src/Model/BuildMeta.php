<?php

namespace PHPCensor\Model;

use PHPCensor\Model\Base\BuildMeta as BaseBuildMeta;
use PHPCensor\Store\BuildStore;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
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
        $buildStore = $this->storeRegistry->get('Build');

        return $buildStore->getById($buildId);
    }
}
