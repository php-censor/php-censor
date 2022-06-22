<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Common\Build\BuildErrorInterface;
use PHPCensor\Model;
use PHPCensor\Traits\Model\HasCreateDateTrait;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildError extends Model implements BuildErrorInterface
{
    use HasCreateDateTrait;

    protected array $data = [
        'id'          => null,
        'build_id'    => null,
        'plugin'      => null,
        'file'        => null,
        'line_start'  => null,
        'line_end'    => null,
        'severity'    => null,
        'message'     => null,
        'create_date' => null,
        'hash'        => '',
        'is_new'      => 0,
    ];

    protected array $dataTypes = [
        'build_id'    => 'integer',
        'line_start'  => 'integer',
        'line_end'    => 'integer',
        'severity'    => 'integer',
        'create_date' => 'datetime',
        'is_new'      => 'boolean'
    ];

    public function getBuildId(): ?int
    {
        return $this->getDataItem('build_id');
    }

    public function setBuildId(int $value): bool
    {
        return $this->setDataItem('build_id', $value);
    }

    public function getPlugin(): ?string
    {
        return $this->getDataItem('plugin');
    }

    public function setPlugin(string $value): bool
    {
        return $this->setDataItem('plugin', $value);
    }

    public function getFile(): ?string
    {
        return $this->getDataItem('file');
    }

    public function setFile(?string $value): bool
    {
        return $this->setDataItem('file', $value);
    }

    public function getLineStart(): ?int
    {
        return $this->getDataItem('line_start');
    }

    public function setLineStart(?int $value): bool
    {
        return $this->setDataItem('line_start', $value);
    }

    public function getLineEnd(): ?int
    {
        return $this->getDataItem('line_end');
    }

    public function setLineEnd(?int $value): bool
    {
        return $this->setDataItem('line_end', $value);
    }

    public function getSeverity(): ?int
    {
        return $this->getDataItem('severity');
    }

    public function setSeverity(int $value): bool
    {
        return $this->setDataItem('severity', $value);
    }

    public function getMessage(): ?string
    {
        return $this->getDataItem('message');
    }

    public function setMessage(string $value): bool
    {
        return $this->setDataItem('message', $value);
    }

    public function getHash(): ?string
    {
        return $this->getDataItem('hash');
    }

    public function setHash(string $value): bool
    {
        return $this->setDataItem('hash', $value);
    }

    public function getIsNew(): bool
    {
        return $this->getDataItem('is_new');
    }

    public function setIsNew(bool $value): bool
    {
        return $this->setDataItem('is_new', $value);
    }
}
