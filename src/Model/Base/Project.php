<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Project extends Model
{
    public const TYPE_LOCAL = 'local';
    public const TYPE_GIT = 'git';
    public const TYPE_GITHUB = 'github';
    public const TYPE_BITBUCKET = 'bitbucket';
    public const TYPE_GITLAB = 'gitlab';
    public const TYPE_GOGS = 'gogs';
    public const TYPE_HG = 'hg';
    public const TYPE_BITBUCKET_HG = 'bitbucket-hg';
    public const TYPE_BITBUCKET_SERVER = 'bitbucket-server';
    public const TYPE_SVN = 'svn';

    public const MIN_BUILD_PRIORITY = 1;
    public const MAX_BUILD_PRIORITY = 2000;
    public const DEFAULT_BUILD_PRIORITY = 1000;
    public const OFFSET_BETWEEN_BUILD_AND_QUEUE = 24;

    protected array $data = [
        'id' => null,
        'title' => null,
        'reference' => null,
        'default_branch' => null,
        'default_branch_only' => 0,
        'ssh_private_key' => null,
        'ssh_public_key' => null,
        'type' => null,
        'access_information' => null,
        'build_config' => null,
        'overwrite_build_config' => 1,
        'allow_public_status' => 0,
        'archived' => 0,
        'group_id' => 1,
        'create_date' => null,
        'user_id' => null,
    ];

    protected array $casts = [
        'allow_public_status' => 'boolean',
        'archived' => 'boolean',
        'group_id' => 'integer',
        'default_branch_only' => 'boolean',
        'create_date' => 'datetime',
        'user_id' => 'integer',
        'overwrite_build_config' => 'boolean'
    ];

    public static array $allowedTypes = [
        self::TYPE_LOCAL,
        self::TYPE_GIT,
        self::TYPE_GITHUB,
        self::TYPE_BITBUCKET,
        self::TYPE_GITLAB,
        self::TYPE_GOGS,
        self::TYPE_HG,
        self::TYPE_BITBUCKET_HG,
        self::TYPE_SVN,
        self::TYPE_BITBUCKET_SERVER
    ];

    public function getTitle(): ?string
    {
        return $this->getData('title');
    }

    public function setTitle(string $value): bool
    {
        return $this->setData('title', $value);
    }

    public function getReference(): ?string
    {
        return $this->getData('reference');
    }

    public function setReference(string $value): bool
    {
        return $this->setData('reference', $value);
    }

    public function getDefaultBranch(): ?string
    {
        return $this->getData('default_branch');
    }

    public function setDefaultBranch(string $value): bool
    {
        return $this->setData('default_branch', $value);
    }

    public function getDefaultBranchOnly(): bool
    {
        return $this->getData('default_branch_only');
    }

    public function setDefaultBranchOnly(bool $value): bool
    {
        return $this->setData('default_branch_only', $value);
    }

    public function getSshPrivateKey(): ?string
    {
        return $this->getData('ssh_private_key');
    }

    public function setSshPrivateKey(?string $value): bool
    {
        return $this->setData('ssh_private_key', $value);
    }

    public function getSshPublicKey(): ?string
    {
        return $this->getData('ssh_public_key');
    }

    public function setSshPublicKey(?string $value): bool
    {
        return $this->setData('ssh_public_key', $value);
    }

    public function getType(): ?string
    {
        return $this->getData('type');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setType(string $value): bool
    {
        if (!\in_array($value, static::$allowedTypes, true)) {
            throw new InvalidArgumentException(
                'Column "type" must be one of: ' . \join(', ', static::$allowedTypes) . '.'
            );
        }

        return $this->setData('type', $value);
    }

    /**
     * @return mixed
     */
    public function getAccessInformation(string $key = null)
    {
        $data = $this->getData('access_information');
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? null;
    }

    public function setAccessInformation(array $value): bool
    {
        return $this->setData('access_information', $value);
    }

    public function getBuildConfig(): ?string
    {
        return $this->getData('build_config');
    }

    public function setBuildConfig(?string $value): bool
    {
        return $this->setData('build_config', $value);
    }

    public function getOverwriteBuildConfig(): bool
    {
        return $this->getData('overwrite_build_config');
    }

    public function setOverwriteBuildConfig(bool $value): bool
    {
        return $this->setData('overwrite_build_config', $value);
    }

    public function getAllowPublicStatus(): bool
    {
        return $this->getData('allow_public_status');
    }

    public function setAllowPublicStatus(bool $value): bool
    {
        return $this->setData('allow_public_status', $value);
    }

    public function getArchived(): bool
    {
        return $this->getData('archived');
    }

    public function setArchived(bool $value): bool
    {
        return $this->setData('archived', $value);
    }

    public function getGroupId(): int
    {
        return $this->getData('group_id');
    }

    public function setGroupId(int $value): bool
    {
        return $this->setData('group_id', $value);
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
