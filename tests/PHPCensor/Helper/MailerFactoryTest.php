<?php

namespace tests\PHPCensor\Service;

use PHPCensor\Helper\MailerFactory;

/**
 * Unit tests for the ProjectService class.
 * @author Dan Cryer <dan@block8.co.uk>
 */
class MailerFactoryTest extends \PHPUnit\Framework\TestCase
{
   public function setUp()
    {
    }

    public function testExecute_TestGetMailConfig()
    {
        $config = [
            'smtp_address'           => 'mail.example.com',
            'smtp_port'              => 225,
            'smtp_encryption'        => 'tls',
            'smtp_username'          => 'php-censor-user',
            'smtp_password'          => 'php-censor-password',
            'default_mailto_address' => 'admin@php-censor.local',
        ];

        $factory = new MailerFactory(['email_settings' => $config]);

        self::assertEquals($config['smtp_address'], $factory->getMailConfig('smtp_address'));
        self::assertEquals($config['smtp_port'], $factory->getMailConfig('smtp_port'));
        self::assertEquals($config['smtp_encryption'], $factory->getMailConfig('smtp_encryption'));
        self::assertEquals($config['smtp_username'], $factory->getMailConfig('smtp_username'));
        self::assertEquals($config['smtp_password'], $factory->getMailConfig('smtp_password'));
        self::assertEquals($config['default_mailto_address'], $factory->getMailConfig('default_mailto_address'));
    }

    public function testExecute_TestMailer()
    {
        $config = [
            'smtp_address'           => 'mail.example.com',
            'smtp_port'              => 225,
            'smtp_encryption'        => 'tls',
            'smtp_username'          => 'php-censor-user',
            'smtp_password'          => 'php-censor-password',
            'default_mailto_address' => 'admin@php-censor.local',
        ];

        $factory = new MailerFactory(['email_settings' => $config]);
        $mailer = $factory->getSwiftMailerFromConfig();

        self::assertEquals($config['smtp_address'], $mailer->getTransport()->getHost());
        self::assertEquals($config['smtp_port'], $mailer->getTransport()->getPort());
        self::assertEquals('tls', $mailer->getTransport()->getEncryption());
        self::assertEquals($config['smtp_username'], $mailer->getTransport()->getUsername());
        self::assertEquals($config['smtp_password'], $mailer->getTransport()->getPassword());
    }
}
