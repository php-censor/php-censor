<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Environment extends Model
{
    protected array $data = [
        'id'         => null,
        'project_id' => null,
        'name'       => null,
        'branches'   => null,
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
     * @return int
     */
    public function getProjectId()
    {
        return (int)$this->data['project_id'];
    }

    /**
     * @return bool
     */
    public function setProjectId(int $value)
    {
        if ($this->data['project_id'] === $value) {
            return false;
        }

        $this->data['project_id'] = $value;

        return $this->setModified('project_id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @return bool
     */
    public function setName(string $value)
    {
        if ($this->data['name'] === $value) {
            return false;
        }

        $this->data['name'] = $value;

        return $this->setModified('name');
    }

    /**
     * @return array
     */
    public function getBranches()
    {
        return \array_filter(
            \array_map(
                'trim',
                \explode("\n", $this->data['branches'])
            )
        );
    }

    /**
     * @return bool
     */
    public function setBranches(array $value)
    {
        $branches = \implode("\n", $value);
        if ($this->data['branches'] === $branches) {
            return false;
        }

        $this->data['branches'] = $branches;

        return $this->setModified('branches');
    }
}
