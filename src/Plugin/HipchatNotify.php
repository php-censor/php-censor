<?php

namespace PHPCensor\Plugin;

use HipChat\HipChat;
use PHPCensor\Builder;
use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Hipchat Plugin
 *
 * @author James Inman <james@jamesinman.co.uk>
 */
class HipchatNotify extends Plugin
{
    protected $authToken;
    protected $color;
    protected $notify;
    protected $userAgent;
    protected $cookie;
    protected $message;
    protected $room;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'hipchat_notify';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $version         = $this->builder->interpolate('%SYSTEM_VERSION%');
        $this->userAgent = 'PHP Censor/' . $version;
        $this->cookie    = "php-censor-cookie";

        if (!\is_array($options) || !isset($options['room']) || (!isset($options['authToken']) && !isset($options['auth_token']))) {
            throw new InvalidArgumentException('Please define room and authToken for hipchat_notify plugin.');
        }

        if (\array_key_exists('auth_token', $options)) {
            $this->authToken = $options['auth_token'];
        }

        $this->room = $options['room'];

        if (isset($options['message'])) {
            $this->message = $options['message'];
        } else {
            $this->message = '%PROJECT_TITLE% built at %BUILD_LINK%';
        }

        if (isset($options['color'])) {
            $this->color = $options['color'];
        } else {
            $this->color = 'yellow';
        }

        if (isset($options['notify'])) {
            $this->notify = $options['notify'];
        } else {
            $this->notify = false;
        }
    }

    /**
     * Run the HipChat plugin.
     * @return bool
     */
    public function execute()
    {
        $hipChat = new HipChat($this->authToken);
        $message = $this->builder->interpolate($this->message);

        $result = true;
        if (is_array($this->room)) {
            foreach ($this->room as $room) {
                if (!$hipChat->message_room($room, 'PHP Censor', $message, $this->notify, $this->color)) {
                    $result = false;
                }
            }
        } else {
            if (!$hipChat->message_room($this->room, 'PHP Censor', $message, $this->notify, $this->color)) {
                $result = false;
            }
        }

        return $result;
    }
}
