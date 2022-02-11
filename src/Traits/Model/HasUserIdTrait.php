<?php

declare(strict_types=1);

namespace PHPCensor\Traits\Model;

use PHPCensor\Model;

/**
 * @mixin Model
 */
trait HasUserIdTrait
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return (null !== $this->data['user_id']) ? (int)$this->data['user_id'] : null;
    }

    /**
     * @param int|null $value
     * @return bool
     */
    public function setUserId(?int $value): bool
    {
        if ($this->data['user_id'] === $value) {
            return false;
        }

        $this->data['user_id'] = $value;

        return $this->setModified('user_id');
    }
}
