<?php

declare(strict_types=1);

namespace PHPCensor\Helper;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * Class MailerFactory helps to set up and configure a Symfony Mailer object.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class MailerFactory
{
    public const ENCRYPTION_SSL = 'ssl';
    public const ENCRYPTION_TLS = 'tls';

    protected array $emailConfig;

    /**
     * Set the mailer factory configuration.
     */
    public function __construct(mixed $config = [])
    {
        if (!\is_array($config)) {
            $config = [];
        }

        $this->emailConfig = (isset($config['email_settings']) && \is_array($config['email_settings']))
            ? $config['email_settings']
            : [];
    }

    public function getMailer(): MailerInterface
    {
        return new Mailer($this->getTransport());
    }

    /**
     * Returns the transport based on the config:
     * - "smtp_encryption: ssl": SMTP over an implicit TLS connection (SMTPS, usually port 465);
     * - "smtp_encryption: tls": SMTP upgraded with mandatory STARTTLS (usually port 587);
     * - no encryption: plain SMTP, without STARTTLS (same behaviour as SwiftMailer);
     * - no "smtp_address": sendmail.
     */
    public function getTransport(): TransportInterface
    {
        $host = (string)$this->getMailConfig('smtp_address');
        if ('' === $host) {
            return new SendmailTransport();
        }

        $port       = (int)$this->getMailConfig('smtp_port');
        $encryption = \strtolower((string)$this->getMailConfig('smtp_encryption'));

        $transport = new EsmtpTransport($host, $port, self::ENCRYPTION_SSL === $encryption);
        if (self::ENCRYPTION_TLS === $encryption) {
            $transport->setRequireTls(true);
        } elseif (self::ENCRYPTION_SSL !== $encryption) {
            $transport->setAutoTls(false);
        }

        $username = (string)$this->getMailConfig('smtp_username');
        if ('' !== $username) {
            $transport->setUsername($username);
            $transport->setPassword((string)$this->getMailConfig('smtp_password'));
        }

        return $transport;
    }

    /**
     * Return a specific configuration value by key.
     */
    public function getMailConfig(string $configName): mixed
    {
        if (isset($this->emailConfig[$configName]) && '' !== $this->emailConfig[$configName]) {
            return $this->emailConfig[$configName];
        }

        return match ($configName) {
            'default_mailto_address', 'smtp_encryption' => null,
            'smtp_port' => '25',
            default => '',
        };
    }
}
