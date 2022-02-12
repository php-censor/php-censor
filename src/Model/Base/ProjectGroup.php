<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;
use PHPCensor\Traits\Model\HasCreateDateTrait;
use PHPCensor\Traits\Model\HasUserIdTrait;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectGroup extends Model
{
    use HasCreateDateTrait;
    use HasUserIdTrait;

    protected array $data = [
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
}
