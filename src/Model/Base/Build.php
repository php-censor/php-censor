<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Model;
use PHPCensor\Store\BuildStore;
use PHPCensor\Traits\Model\HasCreateDateTrait;
use PHPCensor\Traits\Model\HasUserIdTrait;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Build extends Model
{
    use HasUserIdTrait;
    use HasCreateDateTrait;

    public const STATUS_PENDING = 0;
    public const STATUS_RUNNING = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_FAILED = 3;

    public const SOURCE_UNKNOWN = 0;
    public const SOURCE_MANUAL_WEB = 1;
    public const SOURCE_MANUAL_CONSOLE = 2;
    public const SOURCE_PERIODICAL = 3;
    public const SOURCE_WEBHOOK_PUSH = 4;
    public const SOURCE_WEBHOOK_PULL_REQUEST_CREATED = 5;
    public const SOURCE_WEBHOOK_PULL_REQUEST_UPDATED = 6;
    public const SOURCE_WEBHOOK_PULL_REQUEST_APPROVED = 7;
    public const SOURCE_WEBHOOK_PULL_REQUEST_MERGED = 8;
    public const SOURCE_MANUAL_REBUILD_WEB = 9;
    public const SOURCE_MANUAL_REBUILD_CONSOLE = 10;

    protected array $data = [
        'id' => null,
        'parent_id' => null,
        'project_id' => null,
        'commit_id' => null,
        'status' => null,
        'log' => null,
        'branch' => null,
        'tag' => null,
        'create_date' => null,
        'start_date' => null,
        'finish_date' => null,
        'committer_email' => null,
        'commit_message' => null,
        'extra' => [],
        'environment_id' => null,
        'source' => Build::SOURCE_UNKNOWN,
        'user_id' => null,
        'errors_total' => null,
        'errors_total_previous' => null,
        'errors_new' => null,
    ];

    protected array $casts = [
        'project_id' => 'integer',
        'status' => 'integer',
        'create_date' => 'datetime',
        'start_date' => 'datetime',
        'finish_date' => 'datetime',
        'extra' => 'array',
        'environment_id' => 'integer',
        'source' => 'integer',
        'user_id' => 'integer',
        'errors_total' => 'integer',
        'errors_total_previous' => 'integer',
        'errors_new' => 'integer',
        'parent_id' => 'integer',
    ];

    protected array $allowedStatuses = [
        self::STATUS_PENDING,
        self::STATUS_RUNNING,
        self::STATUS_SUCCESS,
        self::STATUS_FAILED,
    ];

    protected array $allowedSources = [
        self::SOURCE_UNKNOWN,
        self::SOURCE_MANUAL_WEB,
        self::SOURCE_MANUAL_CONSOLE,
        self::SOURCE_MANUAL_REBUILD_WEB,
        self::SOURCE_MANUAL_REBUILD_CONSOLE,
        self::SOURCE_PERIODICAL,
        self::SOURCE_WEBHOOK_PUSH,
        self::SOURCE_WEBHOOK_PULL_REQUEST_CREATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_MERGED,
    ];

    public function getParentId(): ?int
    {
        return $this->getData('parent_id');
    }

    public function setParentId(?int $value): bool
    {
        return $this->setData('parent_id', $value);
    }

    public function getProjectId(): ?int
    {
        return $this->getData('project_id');
    }

    public function setProjectId(int $value): bool
    {
        return $this->setData('project_id', $value);
    }

    public function getCommitId(): ?string
    {
        return $this->getData('commit_id');
    }

    public function setCommitId(string $value): bool
    {
        return $this->setData('commit_id', $value);
    }

    public function getStatus(): ?int
    {
        return $this->getData('status');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setStatus(int $value): bool
    {
        if (!in_array($value, $this->allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'Column "status" must be one of: ' . \join(', ', $this->allowedStatuses) . '.'
            );
        }

        return $this->setData('status', $value);
    }

    public function setStatusPending(): bool
    {
        return $this->setData('status', self::STATUS_PENDING);
    }

    public function setStatusRunning(): bool
    {
        return $this->setData('status', self::STATUS_RUNNING);
    }

    public function setStatusSuccess(): bool
    {
        return $this->setData('status', self::STATUS_SUCCESS);
    }

    public function setStatusFailed(): bool
    {
        return $this->setData('status', self::STATUS_FAILED);
    }

    public function getLog(): ?string
    {
        return $this->getData('log');
    }

    public function setLog(?string $value): bool
    {
        return $this->setData('log', $value);
    }

    public function getBranch(): ?string
    {
        return $this->getData('branch');
    }

    public function setBranch(string $value)
    {
        return $this->setData('branch', $value);
    }

    public function getTag(): ?string
    {
        return $this->getData('tag');
    }

    public function setTag(?string $value): bool
    {
        return $this->setData('tag', $value);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getData('start_date');
    }

    public function setStartDate(DateTime $value): bool
    {
        return $this->setData('start_date', $value);
    }

    public function getFinishDate(): ?DateTime
    {
        return $this->getData('finish_date');
    }

    public function setFinishDate(DateTime $value): bool
    {
        return $this->setData('finish_date', $value);
    }

    public function getCommitterEmail(): ?string
    {
        return $this->getData('committer_email');
    }

    public function setCommitterEmail(?string $value): bool
    {
        return $this->setData('committer_email', $value);
    }

    public function getCommitMessage(): ?string
    {
        return $this->getData('commit_message');
    }

    public function setCommitMessage(?string $value): bool
    {
        return $this->setData('commit_message', $value);
    }

    /**
     * @return mixed
     */
    public function getExtra(string $key = null)
    {
        $data = $this->getData('extra');
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? null;
    }

    public function setExtra(array $value): bool
    {
        return $this->setData('extra', $value);
    }

    /**
     * @param mixed  $value
     */
    public function addExtraValue(string $name, $value): bool
    {
        $extra = $this->getExtra();
        if ($extra === null) {
            $extra = [];
        }
        $extra[$name] = $value;

        return $this->setExtra($extra);
    }

    public function removeExtraValue(string $name): bool
    {
        $extra = $this->getExtra();
        if ($extra === null || !array_key_exists($name, $extra)) {
            return false;
        }
        unset($extra[$name]);

        return $this->setExtra($extra);
    }

    public function getEnvironmentId(): ?int
    {
        return $this->getData('environment_id');
    }

    public function setEnvironmentId(?int $value): bool
    {
        return $this->setData('environment_id', $value);
    }

    public function getSource(): ?int
    {
        return $this->getData('source');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setSource(?int $value): bool
    {
        if (!\in_array($value, $this->allowedSources, true)) {
            throw new InvalidArgumentException(
                'Column "source" must be one of: ' . \join(', ', $this->allowedSources) . '.'
            );
        }

        return $this->setData('source', $value);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getErrorsTotal(): ?int
    {
        if ($this->getData('errors_total') === null &&
            !in_array($this->getStatus(), [self::STATUS_PENDING, self::STATUS_RUNNING], true)) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $this->setErrorsTotal($store->getErrorsCount($this->getId()));
            $store->save($this);
        }

        return $this->getData('errors_total');
    }

    public function setErrorsTotal(int $value): bool
    {
        return $this->setData('errors_total', $value);
    }

    /**
     * @throws Exception
     */
    public function getErrorsTotalPrevious(): ?int
    {
        if ($this->getData('errors_total_previous') === null) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $trend = $store->getBuildErrorsTrend($this->getId(), $this->getProjectId(), $this->getBranch());

            if (isset($trend[1])) {
                $previousBuild = $store->getById((int)$trend[1]['build_id']);
                if ($previousBuild &&
                    !\in_array(
                        $previousBuild->getStatus(),
                        [self::STATUS_PENDING, self::STATUS_RUNNING],
                        true
                    )) {
                    $this->setErrorsTotalPrevious((int)$trend[1]['count']);
                    $store->save($this);
                }
            }
        }

        return $this->getData('errors_total_previous');
    }

    public function setErrorsTotalPrevious(int $value): bool
    {
        return $this->setData('errors_total_previous', $value);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getErrorsNew(): ?int
    {
        if ($this->getData('errors_new') === null) {
            /** @var BuildStore $errorStore */
            $store = $this->storeRegistry->get('Build');

            $this->setErrorsNew(
                (int)$store->getNewErrorsCount($this->getId())
            );

            $store->save($this);
        }

        return $this->getData('errors_new');
    }

    public function setErrorsNew(int $value): bool
    {
        return $this->setData('errors_new', $value);
    }

    public function isDebug(): bool
    {
        return (defined('DEBUG_MODE') && DEBUG_MODE) || $this->getExtra('debug');
    }
}
