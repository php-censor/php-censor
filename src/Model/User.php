<?php

namespace PHPCensor\Model;

use PHPCensor\Config;
use PHPCensor\Model\Base\User as BaseUser;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class User extends BaseUser
{
    /**
     * @return int
     */
    public function getFinalPerPage()
    {
        $perPage = $this->getPerPage();
        if ($perPage) {
            return $perPage;
        }

        return (int)Config::getInstance()->get('php-censor.per_page', 10);
    }
}
