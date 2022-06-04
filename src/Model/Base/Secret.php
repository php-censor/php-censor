<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Model;
use PHPCensor\Traits\Model\HasCreateDateTrait;
use PHPCensor\Traits\Model\HasUserIdTrait;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Secret extends Model
{
    use HasCreateDateTrait;
    use HasUserIdTrait;

    protected array $data = [
        'id'          => null,
        'name'        => null,
        'value'       => null,
        'create_date' => null,
        'user_id'     => null,
    ];

    protected array $dataTypes = [
        'create_date' => 'datetime',
        'user_id'     => 'integer',
    ];

    public function getName(): ?string
    {
        return $this->getDataItem('name');
    }

    public function setName(string $value): bool
    {
        return $this->setDataItem('name', $value);
    }

    public function getValue(): ?string
    {
        return $this->getDataItem('value');
    }

    public function setValue(string $value): bool
    {
        return $this->setDataItem('value', $value);
    }
}
