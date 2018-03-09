<?php

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

class Project extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'project';

    /**
     * @var array
     */
    protected $data = [
        'id'                  => null,
        'title'               => null,
        'reference'           => null,
        'branch'              => null,
        'default_branch_only' => null,
        'ssh_private_key'     => null,
        'ssh_public_key'      => null,
        'type'                => null,
        'access_information'  => null,
        'last_commit'         => null,
        'build_config'        => null,
        'allow_public_status' => null,
        'archived'            => null,
        'group_id'            => null,
        'create_date'         => null,
        'user_id'             => 0,
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        return (integer)$this->data['id'];
    }

    /**
     * @param integer $value
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
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
     */
    public function setTitle($value)
    {
        $this->validateNotNull('title', $value);
        $this->validateString('title', $value);

        if ($this->data['title'] === $value) {
            return;
        }

        $this->data['title'] = $value;

        $this->setModified('title');
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->data['reference'];
    }

    /**
     * @param string $value
     */
    public function setReference($value)
    {
        $this->validateNotNull('reference', $value);
        $this->validateString('reference', $value);

        if ($this->data['reference'] === $value) {
            return;
        }

        $this->data['reference'] = $value;

        $this->setModified('reference');
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        if (!$this->data['branch']) {
            $projectType = $this->getType();
            switch ($projectType) {
                case 'hg':
                    $branch = 'default';
                    break;
                case 'svn':
                    $branch = 'trunk';
                    break;
                default:
                    $branch = 'master';
            }

            return $branch;
        } else {
            return $this->data['branch'];
        }
    }

    /**
     * @param string $value
     */
    public function setBranch($value)
    {
        $this->validateNotNull('branch', $value);
        $this->validateString('branch', $value);

        if ($this->data['branch'] === $value) {
            return;
        }

        $this->data['branch'] = $value;

        $this->setModified('branch');
    }

    /**
     * @return boolean
     */
    public function getDefaultBranchOnly()
    {
        return (boolean)$this->data['default_branch_only'];
    }

    /**
     * @param boolean $value
     */
    public function setDefaultBranchOnly($value)
    {
        $this->validateNotNull('default_branch_only', $value);
        $this->validateBoolean('default_branch_only', $value);

        if ($this->data['default_branch_only'] === $value) {
            return;
        }

        $this->data['default_branch_only'] = (integer)$value;

        $this->setModified('default_branch_only');
    }

    /**
     * @return string
     */
    public function getSshPrivateKey()
    {
        return $this->data['ssh_private_key'];
    }

    /**
     * @param string $value
     */
    public function setSshPrivateKey($value)
    {
        $this->validateString('ssh_private_key', $value);

        if ($this->data['ssh_private_key'] === $value) {
            return;
        }

        $this->data['ssh_private_key'] = $value;

        $this->setModified('ssh_private_key');
    }

    /**
     * @return string
     */
    public function getSshPublicKey()
    {
        return $this->data['ssh_public_key'];
    }

    /**
     * @param string $value
     */
    public function setSshPublicKey($value)
    {
        $this->validateString('ssh_public_key', $value);

        if ($this->data['ssh_public_key'] === $value) {
            return;
        }

        $this->data['ssh_public_key'] = $value;

        $this->setModified('ssh_public_key');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * @param string $value
     */
    public function setType($value)
    {
        $this->validateNotNull('type', $value);
        $this->validateString('type', $value);

        if ($this->data['type'] === $value) {
            return;
        }

        $this->data['type'] = $value;

        $this->setModified('type');
    }

    /**
     * @param string|null $key
     *
     * @return array|string|null
     */
    public function getAccessInformation($key = null)
    {
        $data              = json_decode($this->data['access_information'], true);
        $accessInformation = null;
        if (is_null($key)) {
            $accessInformation = $data;
        } elseif (isset($data[$key])) {
            $accessInformation = $data[$key];
        }

        return $accessInformation;
    }

    /**
     * @param array $value
     */
    public function setAccessInformation(array $value)
    {
        $this->validateNotNull('branches', $value);

        $accessInformation = json_encode($value);
        if ($this->data['access_information'] === $accessInformation) {
            return;
        }

        $this->data['access_information'] = $accessInformation;

        $this->setModified('access_information');
    }


    /**
     * @return string
     */
    public function getLastCommit()
    {
        return $this->data['last_commit'];
    }

    /**
     * @param string $value
     */
    public function setLastCommit($value)
    {
        $this->validateString('last_commit', $value);

        if ($this->data['last_commit'] === $value) {
            return;
        }

        $this->data['last_commit'] = $value;

        $this->setModified('last_commit');
    }

    /**
     * @return string
     */
    public function getBuildConfig()
    {
        return $this->data['build_config'];
    }

    /**
     * @param string $value
     */
    public function setBuildConfig($value)
    {
        $this->validateString('build_config', $value);

        if ($this->data['build_config'] === $value) {
            return;
        }

        $this->data['build_config'] = $value;

        $this->setModified('build_config');
    }

    /**
     * @return boolean
     */
    public function getAllowPublicStatus()
    {
        return (boolean)$this->data['allow_public_status'];
    }

    /**
     * @param boolean $value
     */
    public function setAllowPublicStatus($value)
    {
        $this->validateNotNull('allow_public_status', $value);
        $this->validateBoolean('allow_public_status', $value);

        if ($this->data['allow_public_status'] === $value) {
            return;
        }

        $this->data['allow_public_status'] = (integer)$value;

        $this->setModified('allow_public_status');
    }

    /**
     * @return boolean
     */
    public function getArchived()
    {
        return (boolean)$this->data['archived'];
    }

    /**
     * @param boolean $value
     */
    public function setArchived($value)
    {
        $this->validateNotNull('archived', $value);
        $this->validateBoolean('archived', $value);

        if ($this->data['archived'] === $value) {
            return;
        }

        $this->data['archived'] = (integer)$value;

        $this->setModified('archived');
    }

    /**
     * @return integer
     */
    public function getGroupId()
    {
        return (integer)$this->data['group_id'];
    }

    /**
     * @param integer $value
     */
    public function setGroupId($value)
    {
        $this->validateNotNull('group_id', $value);
        $this->validateInt('group_id', $value);

        if ($this->data['group_id'] === $value) {
            return;
        }

        $this->data['group_id'] = $value;

        $this->setModified('group_id');
    }

    /**
     * @return \DateTime|null
     */
    public function getCreateDate()
    {
        if ($this->data['create_date']) {
            return new \DateTime($this->data['create_date']);
        }

        return null;
    }

    /**
     * @param \DateTime $value
     */
    public function setCreateDate(\DateTime $value)
    {
        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return;
        }

        $this->data['create_date'] = $stringValue;

        $this->setModified('create_date');
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return (integer)$this->data['user_id'];
    }

    /**
     * @param integer $value
     */
    public function setUserId($value)
    {
        $this->validateNotNull('user_id', $value);
        $this->validateInt('user_id', $value);

        if ($this->data['user_id'] === $value) {
            return;
        }

        $this->data['user_id'] = $value;

        $this->setModified('user_id');
    }
}
