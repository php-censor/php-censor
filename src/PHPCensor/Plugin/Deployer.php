<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2015, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use b8\HttpClient;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Integrates PHPCI with Deployer: https://github.com/rebelinblue/deployer
 * 
 * @author       Dan Cryer <dan@block8.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Deployer extends Plugin
{
    protected $webhookUrl;
    protected $reason;
    protected $updateOnly;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'deployer';
    }
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->reason = 'PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%';
        if (isset($options['webhook_url'])) {
            $this->webhookUrl = $options['webhook_url'];
        }

        if (isset($options['reason'])) {
            $this->reason = $options['reason'];
        }

        $this->updateOnly = isset($options['update_only']) ? (bool) $options['update_only'] : true;
    }

    /**
    * Copies files from the root of the build directory into the target folder
    */
    public function execute()
    {
        if (empty($this->webhookUrl)) {
            $this->builder->logFailure('You must specify a webhook URL.');
            return false;
        }

        $http = new HttpClient();

        $response = $http->post($this->webhookUrl, [
            'reason'      => $this->builder->interpolate($this->reason),
            'source'      => 'PHP Censor',
            'url'         => $this->builder->interpolate('%BUILD_URI%'),
            'branch'      => $this->builder->interpolate('%BRANCH%'),
            'commit'      => $this->builder->interpolate('%COMMIT%'),
            'update_only' => $this->updateOnly,
        ]);

        return $response['success'];
    }
}
