<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;
use PHPCensor\Traits\Model\HasCreateDateTrait;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WebhookRequest extends Model
{
    use HasCreateDateTrait;

    public const WEBHOOK_TYPE_GIT = 'git';
    public const WEBHOOK_TYPE_GITHUB = 'github';
    public const WEBHOOK_TYPE_BITBUCKET = 'bitbucket';
    public const WEBHOOK_TYPE_GITLAB = 'gitlab';
    public const WEBHOOK_TYPE_GOGS = 'gogs';
    public const WEBHOOK_TYPE_HG = 'hg';
    public const WEBHOOK_TYPE_SVN = 'svn';

    protected array $data = [
        'id'           => null,
        'project_id'   => null,
        'webhook_type' => null,
        'payload'      => null,
        'create_date'  => null,
    ];

    protected array $dataTypes = [
        'project_id'  => 'integer',
        'create_date' => 'datetime',
    ];

    public function getProjectId(): ?int
    {
        return $this->getDataItem('project_id');
    }

    public function setProjectId(int $value): bool
    {
        return $this->setDataItem('project_id', $value);
    }

    public function getWebhookType(): ?string
    {
        return $this->getDataItem('webhook_type');
    }

    public function setWebhookType(string $value): bool
    {
        return $this->setDataItem('webhook_type', $value);
    }

    public function getPayload(): ?string
    {
        return $this->getDataItem('payload');
    }

    public function setPayload(?string $value): bool
    {
        return $this->setDataItem('payload', $value);
    }
}
