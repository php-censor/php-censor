<?php

declare(strict_types=1);

namespace PHPCensor\Common\Repository;

use PHPCensor\Common\Secret\SecretInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface SecretRepositoryInterface
{
    /**
     * @param string[] $names
     *
     * @return SecretInterface[]
     */
    public function getByNames(array $names): array;
}
