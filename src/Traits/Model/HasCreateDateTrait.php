<?php

declare(strict_types=1);

namespace PHPCensor\Traits\Model;

use DateTime;
use Exception;
use PHPCensor\Model;

/**
 * @mixin Model
 */
trait HasCreateDateTrait
{
    /**
     * @return DateTime|null
     *
     * @throws Exception
     */
    public function getCreateDate(): ?DateTime
    {
        if ($this->data['create_date']) {
            return new DateTime($this->data['create_date']);
        }

        return null;
    }

    /**
     * @param DateTime $value
     * @return bool
     */
    public function setCreateDate(DateTime $value): bool
    {
        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return false;
        }

        $this->data['create_date'] = $stringValue;

        return $this->setModified('create_date');
    }
}
