<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildError extends Model
{
    public const SEVERITY_CRITICAL = 0;
    public const SEVERITY_HIGH = 1;
    public const SEVERITY_NORMAL = 2;
    public const SEVERITY_LOW = 3;

    protected array $data = [
        'id' => null,
        'build_id' => null,
        'plugin' => null,
        'file' => null,
        'line_start' => null,
        'line_end' => null,
        'severity' => null,
        'message' => null,
        'create_date' => null,
        'hash' => '',
        'is_new' => 0,
    ];

    protected array $casts = [
        'build_id' => 'integer',
        'line_start' => 'integer',
        'line_end' => 'integer',
        'severity' => 'integer',
        'is_new' => 'boolean'
    ];

    public function getBuildId(): ?int
    {
        return $this->getData('build_id');
    }

    public function setBuildId(int $value): bool
    {
        return $this->setData('build_id', $value);
    }

    public function getPlugin(): ?string
    {
        return $this->getData('plugin');
    }

    public function setPlugin(string $value): bool
    {
        return $this->setData('plugin', $value);
    }

    public function getFile(): ?string
    {
        return $this->getData('file');
    }

    public function setFile(?string $value): bool
    {
        return $this->setData('file', $value);
    }

    public function getLineStart(): ?int
    {
        return $this->getData('line_start');
    }

    public function setLineStart(?int $value): bool
    {
        return $this->setData('line_start', $value);
    }

    public function getLineEnd(): ?int
    {
        return $this->getData('line_end');
    }

    public function setLineEnd(?int $value): bool
    {
        return $this->setData('line_end', $value);
    }

    public function getSeverity(): ?int
    {
        return $this->getData('severity');
    }

    public function setSeverity(int $value): bool
    {
        return $this->setData('severity', $value);
    }

    public function getMessage(): ?string
    {
        return $this->getData('message');
    }

    public function setMessage(string $value): bool
    {
        return $this->setData('message', $value);
    }

    public function getCreateDate(): ?DateTime
    {
        return $this->getData('create_date');
    }

    public function setCreateDate(DateTime $value): bool
    {
        return $this->setData('create_date', $value);
    }

    public function getHash(): ?string
    {
        return $this->getData('hash');
    }

    public function setHash(string $value): bool
    {
        return $this->setData('hash', $value);
    }

    public function getIsNew(): bool
    {
        return $this->getData('is_new');
    }

    public function setIsNew(bool $value): bool
    {
        return $this->setData('is_new', $value);
    }
}
