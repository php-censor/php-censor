<?php

namespace PHPCensor\Model;

use PHPCensor\ConfigurationInterface;
use PHPCensor\Model\Base\User as BaseUser;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class User extends BaseUser
{
    public function getFinalPerPage(ConfigurationInterface $configuration): int
    {
        $perPage = $this->getPerPage();
        if ($perPage) {
            return $perPage;
        }

        return (int)$configuration->get('php-censor.per_page', 10);
    }
}
