<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;

class ProjectGroup extends Model
{
    /**
     * @var array
     */
    protected $data = [
        'id'          => null,
        'title'       => null,
        'create_date' => null,
        'user_id'     => null,
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->data['id'];
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function setId(int $value)
    {
        if ($this->data['id'] === $value) {
            return false;
        }

        $this->data['id'] = $value;

        return $this->setModified('id');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->data['title'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setTitle(string $value)
    {
        if ($this->data['title'] === $value) {
            return false;
        }

        $this->data['title'] = $value;

        return $this->setModified('title');
    }

    /**
     * @return DateTime|null
     *
     * @throws Exception
     */
    public function getCreateDate()
    {
        if ($this->data['create_date']) {
            return new DateTime($this->data['create_date']);
        }

        return null;
    }

    /**
     * @param DateTime $value
     *
     * @return bool
     */
    public function setCreateDate(DateTime $value)
    {
        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return false;
        }

        $this->data['create_date'] = $stringValue;

        return $this->setModified('create_date');
    }

    /**
     * @return string|null
     */
    public function getUserId()
    {
        return $this->data['user_id'];
    }

    /**
     * @param int|null $value
     *
     * @return bool
     */
    public function setUserId(?int $value)
    {
        if ($this->data['user_id'] === $value) {
            return false;
        }

        $this->data['user_id'] = $value;

        return $this->setModified('user_id');
    }
}
