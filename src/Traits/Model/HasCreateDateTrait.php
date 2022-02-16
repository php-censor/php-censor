<?php

declare(strict_types=1);

namespace PHPCensor\Traits\Model;

use DateTime;
use PHPCensor\Model;

/**
 * @mixin Model
 */
trait HasCreateDateTrait
{
    public function getCreateDate(): ?DateTime
    {
        return $this->getDataItem('create_date');
    }

    public function setCreateDate(DateTime $value): bool
    {
        return $this->setDataItem('create_date', $value);
    }
}
