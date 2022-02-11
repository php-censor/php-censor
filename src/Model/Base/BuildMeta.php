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
        'id' => null,
        'build_id' => null,
        'meta_key' => null,
        'meta_value' => null,
    ];

    protected array $casts = [
        'build_id' => 'int',
    ];

    public function getBuildId():int
    {
        return $this->getData('build_id');
    }

    public function setBuildId(int $value): bool
    {
        return $this->setData('build_id', $value);
    }

    public function getMetaKey(): ?string
    {
        return $this->getData('meta_key');
    }

    public function setMetaKey(string $value): bool
    {
        return $this->setData('meta_key', $value);
    }

    public function getMetaValue(): ?string
    {
        return $this->getData('meta_value');
    }

    public function setMetaValue(string $value): bool
    {
        return $this->setData('meta_value', $value);
    }
}
