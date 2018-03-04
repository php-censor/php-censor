<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * Campfire Plugin - Allows Campfire API actions. Strongly based on icecube (http://labs.mimmin.com/icecube)
 * 
 * @author André Cianfarani <acianfa@gmail.com>
 */
class Campfire extends Plugin
{
    protected $url;
    protected $authToken;
    protected $userAgent;
    protected $cookie;
    protected $verbose;
    protected $roomId;
    protected $message;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'campfire';
    }
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->message   = $options['message'];
        $this->userAgent = "PHP Censor/1.0";
        $this->cookie    = "php-censor-cookie";

        $buildSettings = $this->builder->getConfig('build_settings');

        if (isset($buildSettings['campfire'])) {
            $campfire        = $buildSettings['campfire'];
            $this->url       = $campfire['url'];
            $this->authToken = $campfire['authToken'];
            $this->roomId    = $campfire['roomId'];
        } else {
            throw new \Exception('No connection parameters given for Campfire plugin');
        }
    }

    /**
     * Run the Campfire plugin.
     * @return bool|mixed
     */
    public function execute()
    {
        $url = APP_URL . "build/view/" . $this->build->getId();
        $message = str_replace("%buildurl%", $url, $this->message);
        $this->joinRoom($this->roomId);
        $status = $this->speak($message, $this->roomId);
        $this->leaveRoom($this->roomId);

        return $status;

    }

    /**
     * Join a Campfire room.
     * @param $roomId
     */
    public function joinRoom($roomId)
    {
        $this->getPageByPost('/room/'.$roomId.'/join.json');
    }

    /**
     * Leave a Campfire room.
     * @param $roomId
     */
    public function leaveRoom($roomId)
    {
        $this->getPageByPost('/room/'.$roomId.'/leave.json');
    }

    /**
     * Send a message to a campfire room.
     * @param $message
     * @param $roomId
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
     * @param $page
     * @param null $data
     * @return bool|mixed
     */
    private function getPageByPost($page, $data = null)
    {
        $url = $this->url . $page;
        // The new API allows JSON, so we can pass
        // PHP data structures instead of old school POST
        $json = json_encode($data);

        // cURL init & config
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($handle, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($handle, CURLOPT_USERPWD, $this->authToken . ':x');
        curl_setopt($handle, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
        curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookie);

        curl_setopt($handle, CURLOPT_POSTFIELDS, $json);
        $output = curl_exec($handle);

        curl_close($handle);

        // We tend to get one space with an otherwise blank response
        $output = trim($output);

        if (strlen($output)) {
            /* Responses are JSON. Decode it to a data structure */
            return json_decode($output);
        }

        // Simple 200 OK response (such as for joining a room)
        return true;
    }
}
