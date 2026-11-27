<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\Helper\MailerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

/**
 * Unit tests for the MailerFactory class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class MailerFactoryTest extends TestCase
{
    private const CONFIG = [
        'smtp_address'           => 'mail.example.com',
        'smtp_port'              => 225,
        'smtp_encryption'        => 'tls',
        'smtp_username'          => 'php-censor-user',
        'smtp_password'          => 'php-censor-password',
        'default_mailto_address' => 'admin@php-censor.local',
    ];

    public function testExecute_TestGetMailConfig(): void
    {
        $factory = new MailerFactory(['email_settings' => self::CONFIG]);

        foreach (self::CONFIG as $key => $value) {
            self::assertEquals($value, $factory->getMailConfig($key));
        }
    }

    public function testExecute_TestGetMailConfigDefaults(): void
    {
        $factory = new MailerFactory([]);

        self::assertSame('', $factory->getMailConfig('smtp_address'));
        self::assertSame('25', $factory->getMailConfig('smtp_port'));
        self::assertNull($factory->getMailConfig('smtp_encryption'));
        self::assertNull($factory->getMailConfig('default_mailto_address'));
    }

    public function testExecute_TestSendmailTransportWithoutSmtpAddress(): void
    {
        $factory = new MailerFactory(['email_settings' => ['smtp_address' => '']]);

        self::assertInstanceOf(SendmailTransport::class, $factory->getTransport());
    }

    public function testExecute_TestSmtpTransport(): void
    {
        $factory   = new MailerFactory(['email_settings' => self::CONFIG]);
        $transport = $factory->getTransport();

        self::assertInstanceOf(EsmtpTransport::class, $transport);

        $stream = $transport->getStream();
        self::assertInstanceOf(SocketStream::class, $stream);
        self::assertSame(self::CONFIG['smtp_address'], $stream->getHost());
        self::assertSame(self::CONFIG['smtp_port'], $stream->getPort());
        self::assertSame(self::CONFIG['smtp_username'], $transport->getUsername());
        self::assertSame(self::CONFIG['smtp_password'], $transport->getPassword());
    }

    /**
     * @return array<string, array{0: string|null, 1: bool, 2: bool, 3: bool}>
     */
    public static function encryptionProvider(): array
    {
        // encryption => [implicit TLS, STARTTLS allowed, STARTTLS required]
        return [
            'ssl'  => ['ssl', true, true, false],
            'SSL'  => ['SSL', true, true, false],
            'tls'  => ['tls', false, true, true],
            'none' => [null, false, false, false],
        ];
    }

    #[DataProvider('encryptionProvider')]
    public function testExecute_TestSmtpEncryption(
        ?string $encryption,
        bool $implicitTls,
        bool $autoTls,
        bool $requireTls
    ): void {
        $factory = new MailerFactory(['email_settings' => [
            'smtp_address'    => 'mail.example.com',
            'smtp_port'       => 587,
            'smtp_encryption' => $encryption,
        ]]);

        $transport = $factory->getTransport();
        self::assertInstanceOf(EsmtpTransport::class, $transport);

        $stream = $transport->getStream();
        self::assertInstanceOf(SocketStream::class, $stream);
        self::assertSame($implicitTls, $stream->isTLS());
        self::assertSame($autoTls, $transport->isAutoTls());
        self::assertSame($requireTls, $transport->isTlsRequired());
        self::assertSame(587, $stream->getPort());
    }
}
