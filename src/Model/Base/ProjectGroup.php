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
class ProjectGroup extends Model
{
    use HasCreateDateTrait;
    use HasUserIdTrait;

    protected array $data = [
        'id'          => null,
        'title'       => null,
        'create_date' => null,
        'user_id'     => null,
    ];

    protected array $dataTypes = [
        'user_id'     => 'integer',
        'create_date' => 'datetime'
    ];

    public function getTitle(): ?string
    {
        return $this->getDataItem('title');
    }

    public function setTitle(string $value): bool
    {
        return $this->setDataItem('title', $value);
    }
}
