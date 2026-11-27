<?php

declare(strict_types=1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildInterface
{
    public const STAGE_SETUP    = 'setup';
    public const STAGE_TEST     = 'test';
    public const STAGE_DEPLOY   = 'deploy';
    public const STAGE_COMPLETE = 'complete';
    public const STAGE_SUCCESS  = 'success';
    public const STAGE_FAILURE  = 'failure';
    public const STAGE_FIXED    = 'fixed';
    public const STAGE_BROKEN   = 'broken';

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

    public function getId(): ?int;

    public function getProjectId(): ?int;

    public function getCommitId(): ?string;

    public function getCommitterEmail(): ?string;

    public function getCommitMessage(): ?string;

    public function getCommitLink(): ?string;

    public function getBranch(): ?string;

    public function getBranchLink(): ?string;

    public function getTag(): ?string;

    public function getEnvironmentId(): ?int;

    public function getSource(): ?int;

    public function getUserId(): ?int;

    /**
     * @return mixed
     */
    public function getExtra(?string $key = null);

    public function getStatus(): ?int;

    public function getLog(): ?string;

    public function getCreateDate(): ?\DateTime;

    public function getStartDate(): ?\DateTime;

    public function getFinishDate(): ?\DateTime;

    /**
     * Returns absolute build path
     */
    public function getBuildPath(): ?string;

    public function isSuccessful(): bool;

    public function getBuildDirectory(): ?string;

    public function getBuildBranchDirectory(): ?string;

    public function isDebug(): bool;

    /**
     * Example: http://php-censor.localhost/build/view/1
     */
    public function getLink(): string;
}
