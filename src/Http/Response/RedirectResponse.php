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
class RedirectResponse extends Response
{
    public function __construct(Response $createFrom = null)
    {
        parent::__construct($createFrom);

        $this->setContent(null);
        $this->setResponseCode(302);
    }

    public function flush()
    {
        parent::flush();
        exit(1);
    }
}
