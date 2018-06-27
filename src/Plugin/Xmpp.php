<?php

namespace PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Plugin;

/**
 * XMPP Notification - Send notification for successful or failure build
 *
 * @author Alexandre Russo <dev.github@ange7.com>
 */
class XMPP extends Plugin
{
    protected $directory;

    /**
     * @var string, username of sender account xmpp
     */
    protected $username;

    /**
     * @var string, alias server of sender account xmpp
     */
    protected $server;

    /**
     * @var string, password of sender account xmpp
     */
    protected $password;

    /**
     * @var string, alias for sender
     */
    protected $alias;

    /**
     * @var string, use tls
     */
    protected $tls;

    /**
     * @var array, list of recipients xmpp accounts
     */
    protected $recipients;

    /**
     * @var string, mask to format date
     */
    protected $dateFormat;

    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'xmpp';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Builder $builder, Build $build, array $options = [])
    {
        parent::__construct($builder, $build, $options);

        $this->username    = '';
        $this->password    = '';
        $this->server      = '';
        $this->alias       = '';
        $this->recipients  = [];
        $this->tls         = false;
        $this->dateFormat = '%c';

        /*
         * Set recipients list
         */
        if (!empty($options['recipients'])) {
            if (is_string($options['recipients'])) {
                $this->recipients = [$options['recipients']];
            } elseif (is_array($options['recipients'])) {
                $this->recipients = $options['recipients'];
            }
        }
    }

    /**
     * Get config format for sendxmpp config file
     *
     * @return string
     */
    protected function getConfigFormat()
    {
        $conf = $this->username;
        if (!empty($this->server)) {
            $conf .= ';'.$this->server;
        }

        $conf .= ' '.$this->password;

        if (!empty($this->alias)) {
            $conf .= ' '.$this->alias;
        }

        return $conf;
    }

    /**
     * Find config file for sendxmpp binary (default is .sendxmpprc)
     */
    public function findConfigFile()
    {
        if (file_exists($this->builder->buildPath . '.sendxmpprc')) {
            if (md5(file_get_contents($this->builder->buildPath . '.sendxmpprc'))
                !== md5($this->getConfigFormat())) {
                return null;
            }

            return true;
        }

        return null;
    }

    /**
    * Send notification message.
    */
    public function execute()
    {
        $sendxmpp = $this->findBinary('sendxmpp');

        /*
         * Without recipients we can't send notification
         */
        if (count($this->recipients) == 0) {
            return false;
        }

        /*
         * Try to build conf file
         */
        $configFile = $this->builder->buildPath . '.sendxmpprc';
        if (is_null($this->findConfigFile())) {
            file_put_contents($configFile, $this->getConfigFormat());
            chmod($configFile, 0600);
        }

        /*
         * Enabled ssl for connection
         */
        $tls = '';
        if ($this->tls) {
            $tls = ' -t';
        }

        $messageFile = $this->builder->buildPath . uniqid('xmppmessage');
        if ($this->buildMessage($messageFile) === false) {
            return false;
        }

        /*
         * Send XMPP notification for all recipients
         */
        $cmd = $sendxmpp . "%s -f %s -m %s %s";
        $recipients = implode(' ', $this->recipients);

        $success = $this->builder->executeCommand($cmd, $tls, $configFile, $messageFile, $recipients);

        print $this->builder->getLastOutput();

        /*
         * Remove temp message file
         */
        $this->builder->executeCommand("rm -rf ".$messageFile);

        return $success;
    }

    /**
     * @param $messageFile
     * @return int
     */
    protected function buildMessage($messageFile)
    {
        if ($this->build->isSuccessful()) {
            $message = "✔ [".$this->build->getProjectTitle()."] Build #" . $this->build->getId()." successful";
        } else {
            $message = "✘ [".$this->build->getProjectTitle()."] Build #" . $this->build->getId()." failure";
        }

        $message .= ' ('.strftime($this->dateFormat).')';

        return file_put_contents($messageFile, $message);
    }
}
