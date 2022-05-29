<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Helper\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    private ConfigurationInterface $configuration;
    private Email $email;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->email  = new Email($this->configuration);
    }

    public function testGetFrom(): void
    {
        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->with('php-censor.email_settings.from_address', Email::DEFAULT_FROM)
            ->willReturn('Test <test@test.test>');

        self::assertEquals([
            'test@test.test' => 'Test',
        ], $this->email->getFrom());
    }

    public function testGetFrom2(): void
    {
        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->with('php-censor.email_settings.from_address', Email::DEFAULT_FROM)
            ->willReturn('test@test.test');

        self::assertEquals([
            'test@test.test' => 'PHP Censor',
        ], $this->email->getFrom());
    }
}
