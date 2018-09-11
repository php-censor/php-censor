<?php

namespace PHPCensor\Model\Base;

use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;

class Project extends Model
{
    const TYPE_LOCAL        = 'local';
    const TYPE_GIT          = 'git';
    const TYPE_GITHUB       = 'github';
    const TYPE_BITBUCKET    = 'bitbucket';
    const TYPE_GITLAB       = 'gitlab';
    const TYPE_GOGS         = 'gogs';
    const TYPE_HG           = 'hg';
    const TYPE_BITBUCKET_HG = 'bitbucket-hg';
    const TYPE_SVN          = 'svn';

    /**
     * @var string
     */
    protected $tableName = 'project';

    /**
     * @var array
     */
    protected $data = [
        'id'                     => null,
        'title'                  => null,
        'reference'              => null,
        'branch'                 => null,
        'default_branch_only'    => 0,
        'ssh_private_key'        => null,
        'ssh_public_key'         => null,
        'type'                   => null,
        'access_information'     => null,
        'build_config'           => null,
        'overwrite_build_config' => 1,
        'allow_public_status'    => 0,
        'archived'               => 0,
        'group_id'               => 1,
        'create_date'            => null,
        'user_id'                => 0,
    ];

    /**
     * @var array
     */
    public static $allowedTypes = [
        self::TYPE_LOCAL,
        self::TYPE_GIT,
        self::TYPE_GITHUB,
        self::TYPE_BITBUCKET,
        self::TYPE_GITLAB,
        self::TYPE_GOGS,
        self::TYPE_HG,
        self::TYPE_BITBUCKET_HG,
        self::TYPE_SVN,
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
     *
     * @return boolean
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);

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
     * @return boolean
     */
    public function setTitle($value)
    {
        $this->validateNotNull('title', $value);
        $this->validateString('title', $value);

        if ($this->data['title'] === $value) {
            return false;
        }

        $this->data['title'] = $value;

        return $this->setModified('title');
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
     *
     * @return boolean
     */
    public function setReference($value)
    {
        $this->validateNotNull('reference', $value);
        $this->validateString('reference', $value);

        if ($this->data['reference'] === $value) {
            return false;
        }

        $this->data['reference'] = $value;

        return $this->setModified('reference');
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
     *
     * @return boolean
     */
    public function setBranch($value)
    {
        $this->validateNotNull('branch', $value);
        $this->validateString('branch', $value);

        if ($this->data['branch'] === $value) {
            return false;
        }

        $this->data['branch'] = $value;

        return $this->setModified('branch');
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
     *
     * @return boolean
     */
    public function setDefaultBranchOnly($value)
    {
        $this->validateNotNull('default_branch_only', $value);
        $this->validateBoolean('default_branch_only', $value);

        if ($this->data['default_branch_only'] === (integer)$value) {
            return false;
        }

        $this->data['default_branch_only'] = (integer)$value;

        return $this->setModified('default_branch_only');
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
     *
     * @return boolean
     */
    public function setSshPrivateKey($value)
    {
        $this->validateString('ssh_private_key', $value);

        if ($this->data['ssh_private_key'] === $value) {
            return false;
        }

        $this->data['ssh_private_key'] = $value;

        return $this->setModified('ssh_private_key');
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
     *
     * @return boolean
     */
    public function setSshPublicKey($value)
    {
        $this->validateString('ssh_public_key', $value);

        if ($this->data['ssh_public_key'] === $value) {
            return false;
        }

        $this->data['ssh_public_key'] = $value;

        return $this->setModified('ssh_public_key');
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
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function setType($value)
    {
        $this->validateNotNull('type', $value);
        $this->validateString('type', $value);

        if (!in_array($value, static::$allowedTypes, true)) {
            throw new InvalidArgumentException(
                'Column "type" must be one of: ' . join(', ', static::$allowedTypes) . '.'
            );
        }

        if ($this->data['type'] === $value) {
            return false;
        }

        $this->data['type'] = $value;

        return $this->setModified('type');
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
     *
     * @return boolean
     */
    public function setAccessInformation(array $value)
    {
        $this->validateNotNull('branches', $value);

        $accessInformation = json_encode($value);
        if ($this->data['access_information'] === $accessInformation) {
            return false;
        }

        $this->data['access_information'] = $accessInformation;

        return $this->setModified('access_information');
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
     *
     * @return boolean
     */
    public function setBuildConfig($value)
    {
        $this->validateString('build_config', $value);

        if ($this->data['build_config'] === $value) {
            return false;
        }

        $this->data['build_config'] = $value;

        return $this->setModified('build_config');
    }

    /**
     * @return boolean
     */
    public function getOverwriteBuildConfig()
    {
        return (boolean)$this->data['overwrite_build_config'];
    }

    /**
     * @param boolean $value
     *
     * @return boolean
     */
    public function setOverwriteBuildConfig($value)
    {
        $this->validateNotNull('overwrite_build_config', $value);
        $this->validateBoolean('overwrite_build_config', $value);

        if ($this->data['overwrite_build_config'] === (integer)$value) {
            return false;
        }

        $this->data['overwrite_build_config'] = (integer)$value;

        return $this->setModified('overwrite_build_config');
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
     *
     * @return boolean
     */
    public function setAllowPublicStatus($value)
    {
        $this->validateNotNull('allow_public_status', $value);
        $this->validateBoolean('allow_public_status', $value);

        if ($this->data['allow_public_status'] === (integer)$value) {
            return false;
        }

        $this->data['allow_public_status'] = (integer)$value;

        return $this->setModified('allow_public_status');
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
     *
     * @return boolean
     */
    public function setArchived($value)
    {
        $this->validateNotNull('archived', $value);
        $this->validateBoolean('archived', $value);

        if ($this->data['archived'] === (integer)$value) {
            return false;
        }

        $this->data['archived'] = (integer)$value;

        return $this->setModified('archived');
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
     *
     * @return boolean
     */
    public function setGroupId($value)
    {
        $this->validateNotNull('group_id', $value);
        $this->validateInt('group_id', $value);

        if ($this->data['group_id'] === $value) {
            return false;
        }

        $this->data['group_id'] = $value;

        return $this->setModified('group_id');
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
     *
     * @return boolean
     */
    public function setCreateDate(\DateTime $value)
    {
        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return false;
        }

        $this->data['create_date'] = $stringValue;

        return $this->setModified('create_date');
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
     *
     * @return boolean
     */
    public function setUserId($value)
    {
        $this->validateNotNull('user_id', $value);
        $this->validateInt('user_id', $value);

        if ($this->data['user_id'] === $value) {
            return false;
        }

        $this->data['user_id'] = $value;

        return $this->setModified('user_id');
    }
}
