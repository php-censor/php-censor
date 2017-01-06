<?php

/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace Tests\PHPCensor\Plugin\Util\Fake;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

class ExamplePluginFull extends Plugin {
    /**
     * @var array
     */
    public $Options;

    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        $this->Options = $options;
    }

    public function execute()
    {

    }
}
