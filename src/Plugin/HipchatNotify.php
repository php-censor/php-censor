<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use HipChat\HipChat;

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

        $this->userAgent = "PHP Censor/1.0";
        $this->cookie    = "php-censor-cookie";

        if (is_array($options) && isset($options['authToken']) && isset($options['room'])) {
            $this->authToken = $options['authToken'];
            $this->room = $options['room'];

            if (isset($options['message'])) {
                $this->message = $options['message'];
            } else {
                $this->message = '%PROJECT_TITLE% built at %BUILD_URI%';
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
        } else {
            throw new \Exception('Please define room and authToken for hipchat_notify plugin.');
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
