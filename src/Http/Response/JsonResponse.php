<?php

namespace PHPCensor\Http\Response;

use PHPCensor\Http\Response;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class JsonResponse extends Response
{
    public function __construct(Response $createFrom = null)
    {
        parent::__construct($createFrom);

        $this->setContent([]);
        $this->setHeader('Content-Type', 'application/json');
    }

    protected function flushBody()
    {
        if (isset($this->data['body'])) {
            return \json_encode($this->data['body']);
        }

        return \json_encode(null);
    }
}
