<?php

namespace Tests\PHPCensor\Helper;

use PHPCensor\Config;
use PHPCensor\Helper\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * @var Email
     */
    private $email;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->createMock(Config::class);
        $this->email  = new Email($this->config);
    }

    public function testGetFrom()
    {
        $this->config
            ->expects($this->once())
            ->method('get')
            ->with('php-censor.email_settings.from_address', Email::DEFAULT_FROM)
            ->willReturn('Test <test@test.test>');

        self::assertEquals([
            'test@test.test' => 'Test',
        ], $this->email->getFrom());
    }

    public function testGetFrom2()
    {
        $this->config
            ->expects($this->once())
            ->method('get')
            ->with('php-censor.email_settings.from_address', Email::DEFAULT_FROM)
            ->willReturn('test@test.test');

        self::assertEquals([
            'test@test.test' => 'PHP Censor',
        ], $this->email->getFrom());
    }
}
