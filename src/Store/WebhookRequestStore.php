<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PHPCensor\Model\WebhookRequest;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WebhookRequestStore extends Store
{
    protected string $tableName = 'webhook_requests';

    protected string $modelName = WebhookRequest::class;
}
