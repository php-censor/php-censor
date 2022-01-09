<?php

namespace PHPCensor\Plugin;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPCensor\Builder;
use PHPCensor\Exception\HttpException;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Webhook notify Plugin
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Lee Willis (Ademti Software) : https://www.ademti-software.co.uk
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class WebhookNotify extends Plugin
{
    /**
     * @var string The URL to send the webhook to.
     */
    private $url;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'webhook_notify';
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (!\is_array($options)) {
            throw new InvalidArgumentException('Please configure the options for the webhook_notify plugin!');
        }

        if (!isset($options['url'])) {
            throw new InvalidArgumentException('Please define the url for webhook_notify plugin!');
        }
        $this->url = \trim($options['url']);
    }

    /**
     * Run the plugin.
     *
     * @return bool
     * @throws HttpException
     */
    public function execute()
    {
        $payload = [
            'project_id'      => $this->build->getProjectId(),
            'project_title'   => $this->build->getProjectTitle(),
            'build_id'        => $this->build->getId(),
            'commit_id'       => $this->build->getCommitId(),
            'short_commit_id' => substr($this->build->getCommitId(), 0, 7),
            'branch'          => $this->build->getBranch(),
            'branch_link'     => $this->build->getBranchLink(),
            'committer_email' => $this->build->getCommitterEmail(),
            'commit_message'  => $this->build->getCommitMessage(),
            'commit_link'     => $this->build->getCommitLink(),
            'build_link'      => $this->builder->interpolate('%BUILD_LINK%'),
            'project_link'    => $this->builder->interpolate('%PROJECT_LINK%'),
            'status_code'     => $this->build->getStatus(),
            'readable_status' => $this->getReadableStatus($this->build->getStatus()),
        ];


        try {
            $version   = $this->builder->interpolate('%SYSTEM_VERSION%');
            $userAgent = 'PHP Censor/' . $version;
            $client    = new Client([
                'headers' => [
                    'User-Agent' => $userAgent,
                ],
            ]);
            $client->request(
                'POST',
                $this->url,
                ['json' => $payload]
            );
        } catch (GuzzleException $e) {
            return false;
        }

        return true;
    }

    private function getReadableStatus($statusId)
    {
        switch ($statusId) {
            case self::STATUS_PENDING:
                return 'Pending';
                break;
            case self::STATUS_RUNNING:
                return 'Running';
                break;
            case self::STATUS_SUCCESS:
                return 'Successful';
                break;
            case self::STATUS_FAILED:
                return 'Failed';
                break;
        }

        return \sprintf('Unknown (%d)', $statusId);
    }
}
