<?php

namespace PHPCensor\Plugin\Util;

class BitbucketNotifyPhpUnitResult extends BitbucketNotifyPluginResult
{
    public function __construct($plugin, $left, $right)
    {
        parent::__construct($plugin, $left, $right);
        $this->outputFormat = "%s | %01.2f\t=> %01.2f\t%s";
    }

    public function isImproved()
    {
        return $this->right > $this->left;
    }

    public function isDegraded()
    {
        return $this->right < $this->left;
    }

    protected function getTaskDescriptionMessage()
    {
        return 'pls fix %s because the coverage has decreased from %d to %d';
    }
}
