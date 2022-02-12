<?php

declare(strict_types=1);

namespace PHPCensor\Traits\Model;

use PHPCensor\Model;

/**
 * @mixin Model
 */
trait HasUserIdTrait
{
    public function getUserId(): ?int
    {
        return $this->getData('user_id');
    }

    public function setUserId(?int $value): bool
    {
        return $this->setData('user_id', $value);
    }
}
