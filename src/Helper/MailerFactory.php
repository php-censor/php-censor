<?php

namespace PHPCensor\Helper;

use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

/**
 * Class MailerFactory helps to set up and configure a SwiftMailer object.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class MailerFactory
{
    /**
     * @var array
     */
    protected $emailConfig;

    /**
     * Set the mailer factory configuration.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!\is_array($config)) {
            $config = [];
        }

        $this->emailConfig  = isset($config['email_settings']) ? $config['email_settings'] : [];
    }

    /**
     * Returns an instance of Swift_Mailer based on the config.s
     * @return Swift_Mailer
     */
    public function getSwiftMailerFromConfig()
    {
        if ($this->getMailConfig('smtp_address')) {
            $encryptionType = (string)$this->getMailConfig('smtp_encryption');
            if (!$encryptionType) {
                $encryptionType = null;
            }

            /** @var Swift_SmtpTransport $transport */
            $transport = new Swift_SmtpTransport(
                $this->getMailConfig('smtp_address'),
                $this->getMailConfig('smtp_port'),
                $encryptionType
            );

            $transport->setUsername($this->getMailConfig('smtp_username'));
            $transport->setPassword($this->getMailConfig('smtp_password'));
        } else {
            $transport = new Swift_SendmailTransport();
        }

        return new Swift_Mailer($transport);
    }

    /**
     * Return a specific configuration value by key.
     *
     *
     * @return string|null
     */
    public function getMailConfig($configName)
    {
        if (isset($this->emailConfig[$configName]) && '' !== $this->emailConfig[$configName]) {
            return $this->emailConfig[$configName];
        } else {
            switch ($configName) {
                case 'default_mailto_address':
                case 'smtp_encryption':
                    return null;
                case 'smtp_port':
                    return '25';
                case 'smtp_address':
                default:
                    return '';
            }
        }
    }
}
