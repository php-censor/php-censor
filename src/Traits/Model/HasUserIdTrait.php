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
        return $this->getDataItem('user_id');
    }

    public function setUserId(?int $value): bool
    {
        return $this->setDataItem('user_id', $value);
    }
}
