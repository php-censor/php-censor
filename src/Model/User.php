<?php

namespace PHPCensor\Model;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Model\Base\User as BaseUser;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class User extends BaseUser
{
    public function getFinalPerPage(ConfigurationInterface $configuration): ?int
    {
        $perPage = $this->getPerPage();
        if ($perPage) {
            return $perPage;
        }

        return (int)$configuration->get('php-censor.per_page', 10);
    }
}
