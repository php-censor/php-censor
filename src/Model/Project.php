<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use PHPCensor\Store\Factory;
use PHPCensor\Store\EnvironmentStore;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Dumper as YamlDumper;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
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
        'type'                => null,
        'access_information'  => null,
        'last_commit'         => null,
        'build_config'        => null,
        'ssh_public_key'      => null,
        'allow_public_status' => null,
        'archived'            => null,
        'group_id'            => null,
        'create_date'         => null,
        'user_id'             => 0,
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
        'default_branch_only' => 'getDefaultBranchOnly',
        'ssh_private_key'     => 'getSshPrivateKey',
        'type'                => 'getType',
        'access_information'  => 'getAccessInformation',
        'last_commit'         => 'getLastCommit',
        'build_config'        => 'getBuildConfig',
        'ssh_public_key'      => 'getSshPublicKey',
        'allow_public_status' => 'getAllowPublicStatus',
        'archived'            => 'getArchived',
        'group_id'            => 'getGroupId',
        'create_date'         => 'getCreateDate',
        'user_id'             => 'getUserId',

        // Foreign key getters:
        'Group' => 'getGroup',
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
        'default_branch_only' => 'setDefaultBranchOnly',
        'ssh_private_key'     => 'setSshPrivateKey',
        'type'                => 'setType',
        'access_information'  => 'setAccessInformation',
        'last_commit'         => 'setLastCommit',
        'build_config'        => 'setBuildConfig',
        'ssh_public_key'      => 'setSshPublicKey',
        'allow_public_status' => 'setAllowPublicStatus',
        'archived'            => 'setArchived',
        'group_id'            => 'setGroupId',
        'create_date'         => 'setCreateDate',
        'user_id'             => 'setUserId',

        // Foreign key setters:
        'Group' => 'setGroup',
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $rtn = $this->data['title'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        $rtn = $this->data['reference'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getSshPrivateKey()
    {
        $rtn = $this->data['ssh_private_key'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $rtn = $this->data['type'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getLastCommit()
    {
        $rtn = $this->data['last_commit'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getBuildConfig()
    {
        $rtn = $this->data['build_config'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getSshPublicKey()
    {
        $rtn = $this->data['ssh_public_key'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getAllowPublicStatus()
    {
        $rtn = $this->data['allow_public_status'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getArchived()
    {
        $rtn = $this->data['archived'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getGroupId()
    {
        $rtn = $this->data['group_id'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getDefaultBranchOnly()
    {
        $rtn = $this->data['default_branch_only'];

        return $rtn;
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
     * @param integer $value
     */
    public function setDefaultBranchOnly($value)
    {
        $this->validateNotNull('default_branch_only', $value);
        $this->validateInt('default_branch_only', $value);

        if ($this->data['default_branch_only'] === $value) {
            return;
        }

        $this->data['default_branch_only'] = $value;

        $this->setModified('default_branch_only');
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
     * @param integer $value
     */
    public function setAllowPublicStatus($value)
    {
        $this->validateNotNull('allow_public_status', $value);
        $this->validateInt('allow_public_status', $value);

        if ($this->data['allow_public_status'] === $value) {
            return;
        }

        $this->data['allow_public_status'] = $value;

        $this->setModified('allow_public_status');
    }

    /**
     * @param integer $value
     */
    public function setArchived($value)
    {
        $this->validateNotNull('archived', $value);
        $this->validateInt('archived', $value);

        if ($this->data['archived'] === $value) {
            return;
        }

        $this->data['archived'] = $value;

        $this->setModified('archived');
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
     * Get the ProjectGroup model for this Project by Id.
     *
     * @return \PHPCensor\Model\ProjectGroup
     */
    public function getGroup()
    {
        $groupId = $this->getGroupId();

        if (empty($groupId)) {
            return null;
        }

        return Factory::getStore('ProjectGroup')->getById($groupId);
    }

    /**
     * Get Build models by ProjectId for this Project.
     *
     * @return \PHPCensor\Model\Build[]
     */
    public function getProjectBuilds()
    {
        return Factory::getStore('Build')->getByProjectId($this->getId());
    }

    /**
     * Return the latest build from a specific branch, of a specific status, for this project.
     *
     * @param string $branch
     * @param null   $status
     *
     * @return mixed|null
     */
    public function getLatestBuild($branch = 'master', $status = null)
    {
        $criteria = ['branch' => $branch, 'project_id' => $this->getId()];

        if (isset($status)) {
            $criteria['status'] = $status;
        }

        $order  = ['id' => 'DESC'];
        $builds = Factory::getStore('Build')->getWhere($criteria, 1, 0, $order);

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
     *
     * @param string $branch
     *
     * @return mixed|null
     */
    public function getPreviousBuild($branch = 'master')
    {
        $criteria = ['branch' => $branch, 'project_id' => $this->getId()];
        $order    = ['id' => 'DESC'];
        $builds   = Factory::getStore('Build')->getWhere($criteria, 1, 1, $order);

        if (is_array($builds['items']) && count($builds['items'])) {
            $previous = array_shift($builds['items']);

            if (isset($previous) && $previous instanceof Build) {
                return $previous;
            }
        }

        return null;
    }

    /**
     * @param string|array $value
     */
    public function setAccessInformation($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->validateString('access_information', $value);

        if ($this->data['access_information'] === $value) {
            return;
        }

        $this->data['access_information'] = $value;

        $this->setModified('access_information');
    }

    /**
     * Get this project's access_information data. Pass a specific key or null for all data.
     *
     * @param string|null $key
     *
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
     * @return \DateTime
     */
    public function getCreateDate()
    {
        $rtn = $this->data['create_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
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
     * @return string
     */
    public function getUserId()
    {
        $rtn = $this->data['user_id'];

        return (integer)$rtn;
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

    /**
     * Get the value of branch.
     *
     * @return string
     */
    public function getBranch()
    {
        if (empty($this->data['branch'])) {
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
     * Return the name of a FontAwesome icon to represent this project, depending on its type.
     *
     * @return string
     */
    public function getIcon()
    {
        switch ($this->getType()) {
            case 'github':
                $icon = 'github';
                break;

            case 'bitbucket':
            case 'bitbucket-hg':
                $icon = 'bitbucket';
                break;

            case 'git':
            case 'gitlab':
            case 'gogs':
            default:
                $icon = 'code-fork';
                break;
        }

        return $icon;
    }

    /**
     * @return EnvironmentStore
     */
    protected function getEnvironmentStore()
    {
        /** @var EnvironmentStore $store */
        $store = Factory::getStore('Environment');
        return $store;
    }

    /**
     * Get Environments
     *
     * @return array contain items with \PHPCensor\Model\Environment
     */
    public function getEnvironmentsObjects()
    {
        $projectId = $this->getId();

        if (empty($projectId)) {
            return null;
        }

        return $this->getEnvironmentStore()->getByProjectId($projectId);
    }

    /**
     * Get Environments
     *
     * @return string[]
     */
    public function getEnvironmentsNames()
    {
        $environments      = $this->getEnvironmentsObjects();
        $environmentsNames = [];
        foreach($environments['items'] as $environment) {
            /** @var Environment $environment */
            $environmentsNames[] = $environment->getName();
        }

        return $environmentsNames;
    }

    /**
     * Get Environments
     *
     * @return string yaml
     */
    public function getEnvironments()
    {
        $environments       = $this->getEnvironmentsObjects();
        $environmentsConfig = [];
        foreach($environments['items'] as $environment) {
            /** @var Environment $environment */
            $environmentsConfig[$environment->getName()] = $environment->getBranches();
        }

        $yamlDumper = new YamlDumper();
        $value      = $yamlDumper->dump($environmentsConfig, 10, 0, true, false);

        return $value;
    }

    /**
     * Set Environments
     *
     * @param string $value yaml
     */
    public function setEnvironments($value)
    {
        $yamlParser          = new YamlParser();
        $environmentsConfig  = $yamlParser->parse($value);
        $environmentsNames   = !empty($environmentsConfig) ? array_keys($environmentsConfig) : [];
        $currentEnvironments = $this->getEnvironmentsObjects();
        $store               = $this->getEnvironmentStore();
        foreach ($currentEnvironments['items'] as $environment) {
            /** @var Environment $environment */
            $key = array_search($environment->getName(), $environmentsNames);
            if ($key !== false) {
                // already exist
                unset($environmentsNames[$key]);
                $environment->setBranches(!empty($environmentsConfig[$environment->getName()]) ? $environmentsConfig[$environment->getName()] : []);
                $store->save($environment);
            } else {
                // remove
                $store->delete($environment);
            }
        }

        if (!empty($environmentsNames)) {
            // add
            foreach ($environmentsNames as $environmentName) {
                $environment = new Environment();
                $environment->setProjectId($this->getId());
                $environment->setName($environmentName);
                $environment->setBranches(!empty($environmentsConfig[$environment->getName()]) ? $environmentsConfig[$environment->getName()] : []);
                $store->save($environment);
            }
        }
    }

    /**
     * @param string $branch
     *
     * @return string[]
     */
    public function getEnvironmentsNamesByBranch($branch)
    {
        $environmentsNames = [];
        $environments      = $this->getEnvironmentsObjects();
        $defaultBranch     = ($branch == $this->getBranch());
        foreach($environments['items'] as $environment) {
            /** @var Environment $environment */
            if ($defaultBranch || in_array($branch, $environment->getBranches())) {
                $environmentsNames[] = $environment->getName();
            }
        }

        return $environmentsNames;
    }

    /**
     * @param string $environmentName
     *
     * @return string[]
     */
    public function getBranchesByEnvironment($environmentName)
    {
        $branches     = [];
        $environments = $this->getEnvironmentsObjects();
        foreach($environments['items'] as $environment) {
            /** @var Environment $environment */
            if ($environmentName == $environment->getName()) {
                return $environment->getBranches();
            }
        }

        return $branches;
    }
}
