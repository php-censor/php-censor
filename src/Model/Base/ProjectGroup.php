<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectGroup extends Model
{
    protected array $data = [
        'id' => null,
        'title' => null,
        'create_date' => null,
        'user_id' => null,
    ];

    protected array $maps = [
        'user_id' => 'int',
    ];

    public function getTitle(): string
    {
        return $this->getData('title');
    }

    public function setTitle(string $value): bool
    {
        return $this->setData('title', $value);
    }

    public function getCreateDate(): ?DateTime
    {
        return $this->getData('create_date');
    }

    public function setCreateDate(DateTime $value): bool
    {
        return $this->setData('create_date', $value);
    }

    public function getUserId(): ?int
    {
        return $this->getData('user_id');
    }

    public function setUserId(?int $value): bool
    {
        return $this->setData('user_id', $value);
    }
}
