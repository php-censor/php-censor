<?php

namespace PHPCensor\Helper;

use PHPCensor\Builder;
use PHPCensor\Common\Application\ConfigurationInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;

/**
 * Helper class for sending emails using email configuration.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Email
{
    final public const DEFAULT_FROM = 'PHP Censor <no-reply@php-censor.local>';

    protected $emailTo = [];
    protected $emailCc = [];
    protected $subject = 'Email from PHP Censor';
    protected $body    = '';
    protected $isHtml  = false;

    public function __construct(protected ConfigurationInterface $config)
    {
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
     *
     * @return array
     */
    public function getFrom()
    {
        $from = $this->config->get(
            'php-censor.email_settings.from_address',
            self::DEFAULT_FROM
        );

        if (!\str_contains((string) $from, '<')) {
            return [\trim((string) $from) => 'PHP Censor'];
        }

        \preg_match('#^(.*?)<(.*?)>$#ui', (string) $from, $fromParts);

        return [\trim($fromParts[2]) => \trim($fromParts[1])];
    }

    /**
     * Send the email.
     *
     * @return int Number of sent emails (0 on failure)
     */
    public function send(?Builder $builder = null): int
    {
        $smtpServer = $this->config->get('php-censor.email_settings.smtp_address');
        if (null !== $builder) {
            $builder->logDebug(\sprintf("SMTP: '%s'", !empty($smtpServer) ? 'true' : 'false'));
        }

        $factory = new MailerFactory($this->config->get('php-censor'));
        $mailer  = $factory->getMailer();

        $from    = $this->getFrom();
        $message = (new SymfonyEmail())
            ->subject((string)$this->subject)
            ->from(new Address((string)\key($from), (string)\current($from)))
            ->to(...$this->toAddresses($this->emailTo));

        if ($this->isHtml) {
            $message->html((string)$this->body);
        } else {
            $message->text((string)$this->body);
        }

        if (\is_array($this->emailCc) && \count($this->emailCc)) {
            $message->cc(...$this->toAddresses($this->emailCc));
        }

        \ob_start();

        try {
            $mailer->send($message);
            $result = 1;
        } catch (TransportExceptionInterface $e) {
            if (null === $builder) {
                \ob_end_clean();

                // Outside of a build (e.g. password reset), let the caller fail as it did with SwiftMailer.
                throw $e;
            }

            $builder->getBuildLogger()->logWarning(\sprintf('Unable to send email: %s', $e->getMessage()));
            $result = 0;
        }

        $rawOutput = \ob_get_clean();

        if ($rawOutput) {
            $builder?->getBuildLogger()->logWarning($rawOutput);
        }

        return $result;
    }

    /**
     * @param array<string, string|null> $addresses Email => name
     *
     * @return Address[]
     */
    private function toAddresses(array $addresses): array
    {
        $result = [];
        foreach ($addresses as $email => $name) {
            $result[] = new Address((string)$email, (string)$name);
        }

        return $result;
    }
}
