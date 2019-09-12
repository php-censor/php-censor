<?php

namespace PHPCensor\Helper;

class Branch
{
    public static function getDefaultBranchName($projectType)
    {
        switch ($projectType) {
            case 'hg':
                return 'default';
            case 'svn':
                return 'trunk';
            default:
                return 'master';
        }
    }
}
