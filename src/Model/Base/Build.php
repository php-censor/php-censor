<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Common\Build\BuildInterface;
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
        'source'                 => BuildInterface::SOURCE_UNKNOWN,
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
        BuildInterface::STATUS_PENDING,
        BuildInterface::STATUS_RUNNING,
        BuildInterface::STATUS_SUCCESS,
        BuildInterface::STATUS_FAILED,
    ];

    protected array $allowedSources = [
        BuildInterface::SOURCE_UNKNOWN,
        BuildInterface::SOURCE_MANUAL_WEB,
        BuildInterface::SOURCE_MANUAL_CONSOLE,
        BuildInterface::SOURCE_MANUAL_REBUILD_WEB,
        BuildInterface::SOURCE_MANUAL_REBUILD_CONSOLE,
        BuildInterface::SOURCE_PERIODICAL,
        BuildInterface::SOURCE_WEBHOOK_PUSH,
        BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_CREATED,
        BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED,
        BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED,
        BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_MERGED,
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
        if (!\in_array($value, $this->allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'Column "status" must be one of: ' . \join(', ', $this->allowedStatuses) . '.'
            );
        }

        return $this->setDataItem('status', $value);
    }

    public function setStatusPending(): bool
    {
        return $this->setDataItem('status', BuildInterface::STATUS_PENDING);
    }

    public function setStatusRunning(): bool
    {
        return $this->setDataItem('status', BuildInterface::STATUS_RUNNING);
    }

    public function setStatusSuccess(): bool
    {
        return $this->setDataItem('status', BuildInterface::STATUS_SUCCESS);
    }

    public function setStatusFailed(): bool
    {
        return $this->setDataItem('status', BuildInterface::STATUS_FAILED);
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
        if ($extra === null || !\array_key_exists($name, $extra)) {
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
            !\in_array($this->getStatus(), [BuildInterface::STATUS_PENDING, BuildInterface::STATUS_RUNNING], true)) {
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
            !\in_array($this->getStatus(), [BuildInterface::STATUS_PENDING, BuildInterface::STATUS_RUNNING], true)) {
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
        return (\defined('DEBUG_MODE') && DEBUG_MODE) || $this->getExtra('debug');
    }

    public function getLink(): string
    {
        return APP_URL . 'build/view/' . $this->getId();
    }
}
