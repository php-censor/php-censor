<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license        https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link            http://www.phptesting.org/
 */

namespace Tests\PHPCensor\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBuildCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPCensor\Command\CreateAdminCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $command;

    /**
     * @var \Symfony\Component\Console\Application|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $application;

    public function setup()
    {
        parent::setup();

        $projectMock = $this->getMock('PHPCensor\\Model\\Project');

        $projectStoreMock = $this->getMockBuilder('PHPCensor\\Store\\ProjectStore')
            ->getMock();
        $projectStoreMock->method('getById')
            ->will($this->returnValueMap([
                [1, 'read', $projectMock],
                [2, 'read', null],
            ]));

        $buildServiceMock = $this->getMockBuilder('PHPCensor\\Service\\BuildService')
            ->disableOriginalConstructor()
            ->getMock();
        $buildServiceMock->method('createBuild')
            ->withConsecutive(
                [$projectMock, null, null, null, null, null],
                [$projectMock, '92c8c6e', null, null, null, null],
                [$projectMock, null, 'master', null, null, null]
            );

        $this->command = $this->getMockBuilder('PHPCensor\\Command\\CreateBuildCommand')
            ->setConstructorArgs([$projectStoreMock, $buildServiceMock])
            ->setMethods(['reloadConfig'])
            ->getMock();

        $this->application = new Application();
    }

    protected function getCommandTester()
    {
        $this->application->add($this->command);

        $command = $this->application->find('php-censor:create-build');
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    public function testExecute()
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute(['projectId' => 1]);
        $commandTester->execute(['projectId' => 1, '--commit' => '92c8c6e']);
        $commandTester->execute(['projectId' => 1, '--branch' => 'master']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteWithUnknownProjectId()
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['projectId' => 2]);
    }
}
