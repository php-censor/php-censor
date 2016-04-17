<?php

namespace b8\View;

use b8\View\Template;

class UserView extends Template
{
    public function __construct($string)
    {
        trigger_error('Use of UserView is now deprecated. Please use Template instead.', E_USER_NOTICE);
        parent::__construct($string);
    }
}