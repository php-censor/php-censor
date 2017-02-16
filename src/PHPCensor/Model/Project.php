<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store;
use b8\Store\Factory;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Project extends Model
{
    /**
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'project';

    /**
     * @var string
     */
    protected $modelName = 'Project';

    /**
     * @var array
     */
    protected $data = [
        'id'                  => null,
        'title'               => null,
        'reference'           => null,
        'branch'              => null,
        'ssh_private_key'     => null,
        'type'                => null,
        'access_information'  => null,
        'last_commit'         => null,
        'build_config'        => null,
        'ssh_public_key'      => null,
        'allow_public_status' => null,
        'archived'            => null,
        'group_id'            => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'                  => 'getId',
        'title'               => 'getTitle',
        'reference'           => 'getReference',
        'branch'              => 'getBranch',
        'ssh_private_key'     => 'getSshPrivateKey',
        'type'                => 'getType',
        'access_information'  => 'getAccessInformation',
        'last_commit'         => 'getLastCommit',
        'build_config'        => 'getBuildConfig',
        'ssh_public_key'      => 'getSshPublicKey',
        'allow_public_status' => 'getAllowPublicStatus',
        'archived'            => 'getArchived',
        'group_id'            => 'getGroupId',
        // Foreign key getters:
        'Group'               => 'getGroup',
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'                  => 'setId',
        'title'               => 'setTitle',
        'reference'           => 'setReference',
        'branch'              => 'setBranch',
        'ssh_private_key'     => 'setSshPrivateKey',
        'type'                => 'setType',
        'access_information'  => 'setAccessInformation',
        'last_commit'         => 'setLastCommit',
        'build_config'        => 'setBuildConfig',
        'ssh_public_key'      => 'setSshPublicKey',
        'allow_public_status' => 'setAllowPublicStatus',
        'archived'            => 'setArchived',
        'group_id'            => 'setGroupId',
        // Foreign key setters:
        'Group'               => 'setGroup',
    ];

    /**
     * @var array
     */
    public $columns = [
        'id' => [
            'type'           => 'int',
            'length'         => 11,
            'primary_key'    => true,
            'auto_increment' => true,
            'default'        => null,
        ],
        'title' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'reference' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'branch' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => 'master',
        ],
        'ssh_private_key' => [
            'type'     => 'text',
            'nullable' => true,
            'default'  => null,
        ],
        'type' => [
            'type'    => 'varchar',
            'length'  => 50,
            'default' => null,
        ],
        'access_information' => [
            'type'     => 'varchar',
            'length'   => 250,
            'nullable' => true,
            'default'  => null,
        ],
        'last_commit' => [
            'type'     => 'varchar',
            'length'   => 250,
            'nullable' => true,
            'default'  => null,
        ],
        'build_config' => [
            'type'     => 'text',
            'nullable' => true,
            'default'  => null,
        ],
        'ssh_public_key' => [
            'type'     => 'text',
            'nullable' => true,
            'default'  => null,
        ],
        'allow_public_status' => [
            'type'   => 'int',
            'length' => 11,
        ],
        'archived' => [
            'type'    => 'tinyint',
            'length'  => 1,
            'default' => null,
        ],
        'group_id' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => 1,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY'           => ['unique' => true, 'columns' => 'id'],
        'idx_project_title' => ['columns' => 'title'],
        'group_id'          => ['columns' => 'group_id'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [
        'project_ibfk_1' => [
            'local_col' => 'group_id',
            'update'    => 'CASCADE',
            'delete'    => '',
            'table'     => 'project_group',
            'col'       => 'id'
        ],
    ];

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getId()
    {
        $rtn    = $this->data['id'];

        return $rtn;
    }

    /**
     * Get the value of Title / title.
     *
     * @return string
     */
    public function getTitle()
    {
        $rtn    = $this->data['title'];

        return $rtn;
    }

    /**
     * Get the value of Reference / reference.
     *
     * @return string
     */
    public function getReference()
    {
        $rtn    = $this->data['reference'];

        return $rtn;
    }

    /**
     * Get the value of SshPrivateKey / ssh_private_key.
     *
     * @return string
     */
    public function getSshPrivateKey()
    {
        $rtn    = $this->data['ssh_private_key'];

        return $rtn;
    }

    /**
     * Get the value of Type / type.
     *
     * @return string
     */
    public function getType()
    {
        $rtn    = $this->data['type'];

        return $rtn;
    }

    /**
     * Get the value of LastCommit / last_commit.
     *
     * @return string
     */
    public function getLastCommit()
    {
        $rtn    = $this->data['last_commit'];

        return $rtn;
    }

    /**
     * Get the value of BuildConfig / build_config.
     *
     * @return string
     */
    public function getBuildConfig()
    {
        $rtn = $this->data['build_config'];

        return $rtn;
    }

    /**
     * Get the value of SshPublicKey / ssh_public_key.
     *
     * @return string
     */
    public function getSshPublicKey()
    {
        $rtn    = $this->data['ssh_public_key'];

        return $rtn;
    }

    /**
     * Get the value of AllowPublicStatus / allow_public_status.
     *
     * @return int
     */
    public function getAllowPublicStatus()
    {
        $rtn = $this->data['allow_public_status'];

        return $rtn;
    }

    /**
     * Get the value of Archived / archived.
     *
     * @return int
     */
    public function getArchived()
    {
        $rtn    = $this->data['archived'];

        return $rtn;
    }

    /**
     * Get the value of GroupId / group_id.
     *
     * @return int
     */
    public function getGroupId()
    {
        $rtn    = $this->data['group_id'];

        return $rtn;
    }

    /**
     * Set the value of Id / id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('Id', $value);
        $this->validateInt('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
    }

    /**
     * Set the value of Title / title.
     *
     * Must not be null.
     * @param $value string
     */
    public function setTitle($value)
    {
        $this->validateNotNull('Title', $value);
        $this->validateString('Title', $value);

        if ($this->data['title'] === $value) {
            return;
        }

        $this->data['title'] = $value;

        $this->setModified('title');
    }

    /**
     * Set the value of Reference / reference.
     *
     * Must not be null.
     * @param $value string
     */
    public function setReference($value)
    {
        $this->validateNotNull('Reference', $value);
        $this->validateString('Reference', $value);

        if ($this->data['reference'] === $value) {
            return;
        }

        $this->data['reference'] = $value;

        $this->setModified('reference');
    }

    /**
     * Set the value of Branch / branch.
     *
     * Must not be null.
     * @param $value string
     */
    public function setBranch($value)
    {
        $this->validateNotNull('Branch', $value);
        $this->validateString('Branch', $value);

        if ($this->data['branch'] === $value) {
            return;
        }

        $this->data['branch'] = $value;

        $this->setModified('branch');
    }

    /**
     * Set the value of SshPrivateKey / ssh_private_key.
     *
     * @param $value string
     */
    public function setSshPrivateKey($value)
    {
        $this->validateString('SshPrivateKey', $value);

        if ($this->data['ssh_private_key'] === $value) {
            return;
        }

        $this->data['ssh_private_key'] = $value;

        $this->setModified('ssh_private_key');
    }

    /**
     * Set the value of Type / type.
     *
     * Must not be null.
     * @param $value string
     */
    public function setType($value)
    {
        $this->validateNotNull('Type', $value);
        $this->validateString('Type', $value);

        if ($this->data['type'] === $value) {
            return;
        }

        $this->data['type'] = $value;

        $this->setModified('type');
    }

    /**
     * Set the value of LastCommit / last_commit.
     *
     * @param $value string
     */
    public function setLastCommit($value)
    {
        $this->validateString('LastCommit', $value);

        if ($this->data['last_commit'] === $value) {
            return;
        }

        $this->data['last_commit'] = $value;

        $this->setModified('last_commit');
    }

    /**
     * Set the value of BuildConfig / build_config.
     *
     * @param $value string
     */
    public function setBuildConfig($value)
    {
        $this->validateString('BuildConfig', $value);

        if ($this->data['build_config'] === $value) {
            return;
        }

        $this->data['build_config'] = $value;

        $this->setModified('build_config');
    }

    /**
     * Set the value of SshPublicKey / ssh_public_key.
     *
     * @param $value string
     */
    public function setSshPublicKey($value)
    {
        $this->validateString('SshPublicKey', $value);

        if ($this->data['ssh_public_key'] === $value) {
            return;
        }

        $this->data['ssh_public_key'] = $value;

        $this->setModified('ssh_public_key');
    }

    /**
     * Set the value of AllowPublicStatus / allow_public_status.
     *
     * Must not be null.
     * @param $value int
     */
    public function setAllowPublicStatus($value)
    {
        $this->validateNotNull('AllowPublicStatus', $value);
        $this->validateInt('AllowPublicStatus', $value);

        if ($this->data['allow_public_status'] === $value) {
            return;
        }

        $this->data['allow_public_status'] = $value;

        $this->setModified('allow_public_status');
    }

    /**
     * Set the value of Archived / archived.
     *
     * Must not be null.
     * @param $value int
     */
    public function setArchived($value)
    {
        $this->validateNotNull('Archived', $value);
        $this->validateInt('Archived', $value);

        if ($this->data['archived'] === $value) {
            return;
        }

        $this->data['archived'] = $value;

        $this->setModified('archived');
    }

    /**
     * Set the value of GroupId / group_id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setGroupId($value)
    {
        $this->validateNotNull('GroupId', $value);
        $this->validateInt('GroupId', $value);

        if ($this->data['group_id'] === $value) {
            return;
        }

        $this->data['group_id'] = $value;

        $this->setModified('group_id');
    }

    /**
     * Get the ProjectGroup model for this Project by Id.
     *
     * @uses \PHPCensor\Store\ProjectGroupStore::getById()
     * @uses \PHPCensor\Model\ProjectGroup
     * @return \PHPCensor\Model\ProjectGroup
     */
    public function getGroup()
    {
        $key = $this->getGroupId();

        if (empty($key)) {
            return null;
        }

        $cacheKey   = 'Cache.ProjectGroup.' . $key;
        $rtn        = $this->cache->get($cacheKey, null);

        if (empty($rtn)) {
            $rtn    = Factory::getStore('ProjectGroup', 'PHPCensor')->getById($key);
            $this->cache->set($cacheKey, $rtn);
        }

        return $rtn;
    }

    /**
     * Set Group - Accepts an ID, an array representing a ProjectGroup or a ProjectGroup model.
     *
     * @param $value mixed
     */
    public function setGroup($value)
    {
        // Is this an instance of ProjectGroup?
        if ($value instanceof ProjectGroup) {
            return $this->setGroupObject($value);
        }

        // Is this an array representing a ProjectGroup item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setGroupId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setGroupId($value);
    }

    /**
     * Set Group - Accepts a ProjectGroup model.
     *
     * @param $value ProjectGroup
     */
    public function setGroupObject(ProjectGroup $value)
    {
        return $this->setGroupId($value->getId());
    }

    /**
     * Get Build models by ProjectId for this Project.
     *
     * @uses \PHPCensor\Store\BuildStore::getByProjectId()
     * @uses \PHPCensor\Model\Build
     * @return \PHPCensor\Model\Build[]
     */
    public function getProjectBuilds()
    {
        return Factory::getStore('Build', 'PHPCensor')->getByProjectId($this->getId());
    }

    /**
     * Get BuildMeta models by ProjectId for this Project.
     *
     * @uses \PHPCensor\Store\BuildMetaStore::getByProjectId()
     * @uses \PHPCensor\Model\BuildMeta
     * @return \PHPCensor\Model\BuildMeta[]
     */
    public function getProjectBuildMetas()
    {
        return Factory::getStore('BuildMeta', 'PHPCensor')->getByProjectId($this->getId());
    }

    /**
     * Return the latest build from a specific branch, of a specific status, for this project.
     * @param string $branch
     * @param null $status
     * @return mixed|null
     */
    public function getLatestBuild($branch = 'master', $status = null)
    {
        $criteria = ['branch' => $branch, 'project_id' => $this->getId()];

        if (isset($status)) {
            $criteria['status'] = $status;
        }

        $order  = ['id' => 'DESC'];
        $builds = Store\Factory::getStore('Build')->getWhere($criteria, 1, 0, [], $order);

        if (is_array($builds['items']) && count($builds['items'])) {
            $latest = array_shift($builds['items']);

            if (isset($latest) && $latest instanceof Build) {
                return $latest;
            }
        }

        return null;
    }

    /**
     * Return the previous build from a specific branch, for this project.
     * @param string $branch
     * @return mixed|null
     */
    public function getPreviousBuild($branch = 'master')
    {
        $criteria = ['branch' => $branch, 'project_id' => $this->getId()];
        $order    = ['id' => 'DESC'];
        $builds   = Store\Factory::getStore('Build')->getWhere($criteria, 1, 1, [], $order);

        if (is_array($builds['items']) && count($builds['items'])) {
            $previous = array_shift($builds['items']);

            if (isset($previous) && $previous instanceof Build) {
                return $previous;
            }
        }

        return null;
    }

    /**
     * Store this project's access_information data
     * @param string|array $value
     */
    public function setAccessInformation($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->validateString('AccessInformation', $value);

        if ($this->data['access_information'] === $value) {
            return;
        }

        $this->data['access_information'] = $value;

        $this->setModified('access_information');
    }

    /**
     * Get this project's access_information data. Pass a specific key or null for all data.
     * @param string|null $key
     * @return mixed|null|string
     */
    public function getAccessInformation($key = null)
    {
        $info = $this->data['access_information'];

        // Handle old-format (serialized) access information first:
        if (!empty($info) && !in_array(substr($info, 0, 1), ['{', '['])) {
            $data = unserialize($info);
        } else {
            $data = json_decode($info, true);
        }

        if (is_null($key)) {
            $rtn = $data;
        } elseif (isset($data[$key])) {
            $rtn = $data[$key];
        } else {
            $rtn = null;
        }

        return $rtn;
    }

    /**
     * Get the value of Branch / branch.
     *
     * @return string
     */
    public function getBranch()
    {
        if (empty($this->data['branch'])) {
            return $this->getType() === 'hg' ? 'default' : 'master';
        } else {
            return $this->data['branch'];
        }
    }

    /**
     * Return the name of a FontAwesome icon to represent this project, depending on its type.
     * @return string
     */
    public function getIcon()
    {
        switch ($this->getType()) {
            case 'github':
                $icon = 'github';
                break;

            case 'bitbucket':
                $icon = 'bitbucket';
                break;

            case 'remote':
            case 'gitlab':
            default:
                $icon = 'code-fork';
                break;
        }

        return $icon;
    }
}
