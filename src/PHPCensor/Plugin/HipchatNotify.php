<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;
use HipChat\HipChat;

/**
 * Hipchat Plugin
 * @author       James Inman <james@jamesinman.co.uk>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class HipchatNotify implements Plugin
{
    protected $authToken;
    protected $color;
    protected $notify;

    /**
     * Set up the plugin, configure options, etc.
     * @param Builder $phpci
     * @param Build $build
     * @param array $options
     * @throws \Exception
     */
    public function __construct(Builder $phpci, Build $build, array $options = [])
    {
        $this->phpci = $phpci;
        $this->build = $build;

        $this->userAgent = "PHP Censor/1.0";
        $this->cookie = "php-censor-cookie";

        if (is_array($options) && isset($options['authToken']) && isset($options['room'])) {
            $this->authToken = $options['authToken'];
            $this->room = $options['room'];

            if (isset($options['message'])) {
                $this->message = $options['message'];
            } else {
                $this->message = Lang::get('x_built_at_x');
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
            throw new \Exception(Lang::get('hipchat_settings'));
        }
    }

    /**
     * Run the HipChat plugin.
     * @return bool
     */
    public function execute()
    {
        $hipChat = new HipChat($this->authToken);
        $message = $this->phpci->interpolate($this->message);

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
