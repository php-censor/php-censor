<?php

namespace PHPCensor\Plugin\Util;

class BitbucketNotifyPhpUnitResult extends BitbucketNotifyPluginResult
{
    public function isImproved()
    {
        return $this->right > $this->left;
    }

    public function isDegraded()
    {
        return $this->right < $this->left;
    }
}
