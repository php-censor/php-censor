<?php

namespace b8\Type;

interface RestUser
{
    public function checkPermission($permission, $resource);
}
