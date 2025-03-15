<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Campfire Plugin - Allows Campfire API actions. Strongly based on icecube (http://labs.mimmin.com/icecube)
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author André Cianfarani <acianfa@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class CampfireNotify extends Plugin
{
    protected $url;
    protected $authToken;
    protected $userAgent;
    protected $cookie;
    protected $verbose = false;
    protected $room;
    protected $message;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'campfire_notify';
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->message   = $options['message'];
        $version         = $this->builder->interpolate('%SYSTEM_VERSION%');
        $this->userAgent = 'PHP Censor/' . $version;
        $this->cookie    = "php-censor-cookie";

        if (isset($options['verbose']) && $options['verbose']) {
            $this->verbose = true;
        }

        $buildSettings = $this->builder->getConfig('build_settings');

        if (isset($buildSettings['campfire_notify'])) {
            $campfire        = $buildSettings['campfire_notify'];
            $this->url       = $campfire['url'];

            if (\array_key_exists('auth_token', $campfire)) {
                $this->authToken = $this->builder->interpolate($campfire['auth_token'], true);
            }

            if (\array_key_exists('room', $campfire)) {
                $this->room = $campfire['room'];
            }
        } else {
            throw new InvalidArgumentException('No connection parameters given for Campfire plugin');
        }
    }

    /**
     * Run the Campfire plugin.
     * @return bool|mixed
     */
    public function execute()
    {
        $this->message = $this->builder->interpolate($this->message);

        $this->joinRoom($this->room);

        $status = $this->speak($this->message, $this->room);

        $this->leaveRoom($this->room);

        return $status;
    }

    /**
     * Join a Campfire room.
     */
    public function joinRoom($roomId)
    {
        $this->getPageByPost('/room/'.$roomId.'/join.json');
    }

    /**
     * Leave a Campfire room.
     */
    public function leaveRoom($roomId)
    {
        $this->getPageByPost('/room/'.$roomId.'/leave.json');
    }

    /**
     * Send a message to a campfire room.
     * @param bool $isPaste
     * @return bool|mixed
     */
    public function speak($message, $roomId, $isPaste = false)
    {
        $page = '/room/'.$roomId.'/speak.json';

        if ($isPaste) {
            $type = 'PasteMessage';
        } else {
            $type = 'TextMessage';
        }

        return $this->getPageByPost($page, ['message' => ['type' => $type, 'body' => $message]]);
    }

    /**
     * Make a request to Campfire.
     * @param null $data
     * @return bool|mixed
     */
    private function getPageByPost($page, $data = null)
    {
        $url = $this->url . $page;
        // The new API allows JSON, so we can pass
        // PHP data structures instead of old school POST
        $json = \json_encode($data);

        // cURL init & config
        $handle = \curl_init();
        \curl_setopt($handle, CURLOPT_URL, $url);
        \curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($handle, CURLOPT_POST, 1);
        \curl_setopt($handle, CURLOPT_USERAGENT, $this->userAgent);
        \curl_setopt($handle, CURLOPT_VERBOSE, $this->verbose);
        \curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
        \curl_setopt($handle, CURLOPT_USERPWD, $this->authToken . ':x');
        \curl_setopt($handle, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
        \curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookie);

        \curl_setopt($handle, CURLOPT_POSTFIELDS, $json);
        $output = \curl_exec($handle);

        \curl_close($handle);

        // We tend to get one space with an otherwise blank response
        $output = \trim($output);

        if (\strlen($output)) {
            /* Responses are JSON. Decode it to a data structure */
            return \json_decode($output);
        }

        // Simple 200 OK response (such as for joining a room)
        return true;
    }
}
