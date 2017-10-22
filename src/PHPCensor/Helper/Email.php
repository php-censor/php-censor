<?php

namespace PHPCensor\Helper;

use b8\Config;
use PHPCensor\Builder;

/**
 * Helper class for sending emails using email configuration.
 */
class Email
{
    const DEFAULT_FROM = 'PHP Censor <no-reply@php-censor.local>';

    protected $emailTo = [];
    protected $emailCc = [];
    protected $subject = 'Email from PHP Censor';
    protected $body    = '';
    protected $isHtml  = false;
    protected $config;

    /**
     * Create a new email object.
     */
    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     * Set the email's To: header.
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setEmailTo($email, $name = null)
    {
        $this->emailTo[$email] = $name;

        return $this;
    }

    /**
     * Add an address to the email's CC header.
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addCc($email, $name = null)
    {
        $this->emailCc[$email] = $name;

        return $this;
    }

    /**
     * Set the email subject.
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the email body.
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set whether or not the email body is HTML.
     * @param bool $isHtml
     * @return $this
     */
    public function setHtml($isHtml = false)
    {
        $this->isHtml = $isHtml;

        return $this;
    }

    /**
     * Get the from address to use for the email.
     * @return mixed|string
     */
    protected function getFrom()
    {
        $from = $this->config->get(
            'php-censor.email_settings.from_address',
            self::DEFAULT_FROM
        );
        
        if (strpos($from, '<') === false) {
            return (string)$from;
        }
        
        preg_match('#^(.*?)<(.*)>$#ui', $from, $fromParts);
        
        return [$fromParts[2] => $fromParts[1]];
    }

    /**
     * Send the email.
     *
     * @param Builder $builder
     *
     * @return integer
     */
    public function send(Builder $builder = null)
    {
        $smtpServer = $this->config->get('php-censor.email_settings.smtp_address');
        if (null !== $builder) {
            $builder->logDebug(sprintf("SMTP: '%s'", !empty($smtpServer) ? 'true' : 'false'));
        }

        $factory = new MailerFactory($this->config->get('php-censor'));
        $mailer = $factory->getSwiftMailerFromConfig();

        $message = \Swift_Message::newInstance($this->subject)
            ->setFrom($this->getFrom())
            ->setTo($this->emailTo)
            ->setBody($this->body);

        if ($this->isHtml) {
            $message->setContentType('text/html');
        }

        if (is_array($this->emailCc) && count($this->emailCc)) {
            $message->setCc($this->emailCc);
        }

        return $mailer->send($message);
    }
}
