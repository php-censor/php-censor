<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildMeta extends Model
{
    protected array $data = [
        'id'         => null,
        'build_id'   => null,
        'meta_key'   => null,
        'meta_value' => null,
    ];

    protected array $dataTypes = [
        'build_id' => 'integer',
    ];

    public function getBuildId(): int
    {
        return $this->getDataItem('build_id');
    }

    public function setBuildId(int $value): bool
    {
        return $this->setDataItem('build_id', $value);
    }

    public function getMetaKey(): ?string
    {
        return $this->getDataItem('meta_key');
    }

    public function setMetaKey(string $value): bool
    {
        return $this->setDataItem('meta_key', $value);
    }

    public function getMetaValue(): ?string
    {
        return $this->getDataItem('meta_value');
    }

    public function setMetaValue(string $value): bool
    {
        return $this->setDataItem('meta_value', $value);
    }
}
