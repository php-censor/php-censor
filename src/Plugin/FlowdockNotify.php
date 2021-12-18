<?php

namespace PHPCensor\Plugin;

use Exception;
use FlowdockClient\Api\Push\Push;
use FlowdockClient\Api\Push\TeamInboxMessage;
use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Flowdock Plugin
 *
 * @author Petr Cervenka <petr@nanosolutions.io>
 */
class FlowdockNotify extends Plugin
{
    protected $authToken;
    protected $email;
    protected $message;

    public const MESSAGE_DEFAULT = 'Build %BUILD_ID% has finished for commit <a href="%COMMIT_LINK%">%SHORT_COMMIT_ID%</a>
                            (%COMMITTER_EMAIL%)> on branch <a href="%BRANCH_LINK%">%BRANCH%</a>';

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'flowdock_notify';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        if (!is_array($options) || (!isset($options['api_key']) && !isset($options['auth_token']))) {
            throw new Exception('Please define the "auth_token" for Flowdock Notify plugin!');
        }

        if (\array_key_exists('auth_token', $options)) {
            $this->authToken = $options['auth_token'];
        }

        $this->message = isset($options['message']) ? $options['message'] : self::MESSAGE_DEFAULT;
        $this->email   = isset($options['email']) ? $options['email'] : 'PHP Censor';
    }

    /**
     * Run the Flowdock plugin.
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        $message         = $this->builder->interpolate($this->message);
        $successfulBuild = $this->build->isSuccessful() ? 'Success' : 'Failed';
        $push            = new Push($this->authToken);
        $flowMessage     = TeamInboxMessage::create()
            ->setSource("PHPCensor")
            ->setFromAddress($this->email)
            ->setFromName($this->build->getProject()->getTitle())
            ->setSubject($successfulBuild)
            ->setTags(['#ci'])
            ->setLink($this->build->getBranchLink())
            ->setContent($message);

        if (!$push->sendTeamInboxMessage($flowMessage, ['connect_timeout' => 5000, 'timeout' => 5000])) {
            throw new Exception(sprintf('Flowdock Failed: %s', $flowMessage->getResponseErrors()));
        }

        return true;
    }
}
