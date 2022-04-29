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
    public const STATUS_FAILED  = 3;

    public const SOURCE_UNKNOWN                       = 0;
    public const SOURCE_MANUAL_WEB                    = 1;
    public const SOURCE_MANUAL_CONSOLE                = 2;
    public const SOURCE_PERIODICAL                    = 3;
    public const SOURCE_WEBHOOK_PUSH                  = 4;
    public const SOURCE_WEBHOOK_PULL_REQUEST_CREATED  = 5;
    public const SOURCE_WEBHOOK_PULL_REQUEST_UPDATED  = 6;
    public const SOURCE_WEBHOOK_PULL_REQUEST_APPROVED = 7;
    public const SOURCE_WEBHOOK_PULL_REQUEST_MERGED   = 8;
    public const SOURCE_MANUAL_REBUILD_WEB            = 9;
    public const SOURCE_MANUAL_REBUILD_CONSOLE        = 10;

    protected array $data = [
        'id'                     => null,
        'parent_id'              => null,
        'project_id'             => null,
        'commit_id'              => null,
        'status'                 => null,
        'log'                    => null,
        'branch'                 => null,
        'tag'                    => null,
        'create_date'            => null,
        'start_date'             => null,
        'finish_date'            => null,
        'committer_email'        => null,
        'commit_message'         => null,
        'extra'                  => [],
        'environment_id'         => null,
        'source'                 => Build::SOURCE_UNKNOWN,
        'user_id'                => null,
        'errors_total'           => null,
        'errors_total_previous'  => null,
        'errors_new'             => null,
        'test_coverage'          => null,
        'test_coverage_previous' => null,
    ];

    protected array $dataTypes = [
        'project_id'            => 'integer',
        'status'                => 'integer',
        'create_date'           => 'datetime',
        'start_date'            => 'datetime',
        'finish_date'           => 'datetime',
        'extra'                 => 'array',
        'environment_id'        => 'integer',
        'source'                => 'integer',
        'user_id'               => 'integer',
        'errors_total'          => 'integer',
        'errors_total_previous' => 'integer',
        'errors_new'            => 'integer',
        'parent_id'             => 'integer',
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
        return $this->getDataItem('parent_id');
    }

    public function setParentId(?int $value): bool
    {
        return $this->setDataItem('parent_id', $value);
    }

    public function getProjectId(): ?int
    {
        return $this->getDataItem('project_id');
    }

    public function setProjectId(int $value): bool
    {
        return $this->setDataItem('project_id', $value);
    }

    public function getCommitId(): ?string
    {
        return $this->getDataItem('commit_id');
    }

    public function setCommitId(string $value): bool
    {
        return $this->setDataItem('commit_id', $value);
    }

    public function getStatus(): ?int
    {
        return $this->getDataItem('status');
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

        return $this->setDataItem('status', $value);
    }

    public function setStatusPending(): bool
    {
        return $this->setDataItem('status', self::STATUS_PENDING);
    }

    public function setStatusRunning(): bool
    {
        return $this->setDataItem('status', self::STATUS_RUNNING);
    }

    public function setStatusSuccess(): bool
    {
        return $this->setDataItem('status', self::STATUS_SUCCESS);
    }

    public function setStatusFailed(): bool
    {
        return $this->setDataItem('status', self::STATUS_FAILED);
    }

    public function getLog(): ?string
    {
        return $this->getDataItem('log');
    }

    public function setLog(?string $value): bool
    {
        return $this->setDataItem('log', $value);
    }

    public function getBranch(): ?string
    {
        return $this->getDataItem('branch');
    }

    public function setBranch(string $value)
    {
        return $this->setDataItem('branch', $value);
    }

    public function getTag(): ?string
    {
        return $this->getDataItem('tag');
    }

    public function setTag(?string $value): bool
    {
        return $this->setDataItem('tag', $value);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getDataItem('start_date');
    }

    public function setStartDate(DateTime $value): bool
    {
        return $this->setDataItem('start_date', $value);
    }

    public function getFinishDate(): ?DateTime
    {
        return $this->getDataItem('finish_date');
    }

    public function setFinishDate(DateTime $value): bool
    {
        return $this->setDataItem('finish_date', $value);
    }

    public function getCommitterEmail(): ?string
    {
        return $this->getDataItem('committer_email');
    }

    public function setCommitterEmail(?string $value): bool
    {
        return $this->setDataItem('committer_email', $value);
    }

    public function getCommitMessage(): ?string
    {
        return $this->getDataItem('commit_message');
    }

    public function setCommitMessage(?string $value): bool
    {
        return $this->setDataItem('commit_message', $value);
    }

    /**
     * @return mixed
     */
    public function getExtra(string $key = null)
    {
        $data = $this->getDataItem('extra');
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? null;
    }

    public function setExtra(array $value): bool
    {
        return $this->setDataItem('extra', $value);
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
        return $this->getDataItem('environment_id');
    }

    public function setEnvironmentId(?int $value): bool
    {
        return $this->setDataItem('environment_id', $value);
    }

    public function getSource(): ?int
    {
        return $this->getDataItem('source');
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

        return $this->setDataItem('source', $value);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getErrorsTotal(): ?int
    {
        if ($this->getDataItem('errors_total') === null &&
            !in_array($this->getStatus(), [self::STATUS_PENDING, self::STATUS_RUNNING], true)) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $this->setErrorsTotal($store->getErrorsCount($this->getId()));
            $store->save($this);
        }

        return $this->getDataItem('errors_total');
    }

    public function setErrorsTotal(int $value): bool
    {
        return $this->setDataItem('errors_total', $value);
    }

    /**
     * @throws Exception
     */
    public function getErrorsTotalPrevious(): ?int
    {
        if ($this->getDataItem('errors_total_previous') === null) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $trend = $store->getBuildErrorsTrend($this->getId(), $this->getProjectId(), $this->getBranch());

            if (isset($trend[0])) {
                $this->setErrorsTotalPrevious((int)$trend[0]['count']);
                $store->save($this);
            }
        }

        return $this->getDataItem('errors_total_previous');
    }

    public function setErrorsTotalPrevious(int $value): bool
    {
        return $this->setDataItem('errors_total_previous', $value);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getTestCoverage(): ?string
    {
        if ($this->getDataItem('test_coverage') === null &&
            !in_array($this->getStatus(), [self::STATUS_PENDING, self::STATUS_RUNNING], true)) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $this->setTestCoverage($store->getTestCoverage($this->getId()));
            $store->save($this);
        }

        return $this->getDataItem('test_coverage');
    }

    public function setTestCoverage(string $value): bool
    {
        return $this->setDataItem('test_coverage', $value);
    }

    /**
     * @throws Exception
     */
    public function getTestCoveragePrevious(): ?string
    {
        if ($this->getDataItem('test_coverage_previous') === null) {
            /** @var BuildStore $store */
            $store = $this->storeRegistry->get('Build');

            $trend = $store->getBuildTestCoverageTrend($this->getId(), $this->getProjectId(), $this->getBranch());

            if (!empty($trend[0]) && !empty($trend[0]['coverage'])) {
                $coverage = \json_decode($trend[0]['coverage'], true);
                if (isset($coverage['lines'])) {
                    $this->setTestCoveragePrevious($coverage['lines']);
                    $store->save($this);
                }
            }
        }

        return $this->getDataItem('test_coverage_previous');
    }

    public function setTestCoveragePrevious(string $value): bool
    {
        return $this->setDataItem('test_coverage_previous', $value);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getErrorsNew(): ?int
    {
        if ($this->getDataItem('errors_new') === null) {
            /** @var BuildStore $errorStore */
            $store = $this->storeRegistry->get('Build');

            $this->setErrorsNew(
                (int)$store->getNewErrorsCount($this->getId())
            );

            $store->save($this);
        }

        return $this->getDataItem('errors_new');
    }

    public function setErrorsNew(int $value): bool
    {
        return $this->setDataItem('errors_new', $value);
    }

    public function isDebug(): bool
    {
        return (defined('DEBUG_MODE') && DEBUG_MODE) || $this->getExtra('debug');
    }
}
