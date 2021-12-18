<?php

namespace Tests\PHPCensor\Command;

use PHPCensor\Command\CreateAdminCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends TestCase
{
    /**
     * @var CreateAdminCommand|MockObject
     */
    protected $command;

    /**
     * @var Application|MockObject
     */
    protected $application;

    /**
     * @var MockObject|QuestionHelper
     */
    protected $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $userStoreMock = $this->getMockBuilder('PHPCensor\\Store\\UserStore')->getMock();

        $this->command = new CreateAdminCommand($userStoreMock);

        $this->helper = $this
            ->getMockBuilder('Symfony\\Component\\Console\\Helper\\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $this->application = new Application();
    }

    /**
     * @return CommandTester
     */
    protected function getCommandTester()
    {
        $this->application->getHelperSet()->set($this->helper, 'question');
        $this->application->add($this->command);
        $commandTester = new CommandTester($this->command);

        return $commandTester;
    }

    public function testExecute()
    {
        $this->helper->method('ask')->will($this->returnValue('test@example.com'));
        $this->helper->method('ask')->will($this->returnValue('A name'));
        $this->helper->method('ask')->will($this->returnValue('foobar123'));

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        self::assertEquals('User account created!' . PHP_EOL, $commandTester->getDisplay());
    }
}
