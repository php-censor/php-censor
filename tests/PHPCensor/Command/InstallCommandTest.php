<?php

namespace Tests\PHPCensor\Plugin\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;

class InstallCommandTest extends \PHPUnit\Framework\TestCase
{
    public $config;

    public $admin;

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        parent::setUp();

        $this->application = new Application();
        $this->application->setHelperSet(new HelperSet());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHelperMock()
    {
        // We check that there's no interaction with user.
        return $this
            ->getMockBuilder('Symfony\\Component\\Console\\Helper\\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInstallCommandMock()
    {
        // Current command, we need to mock all method that interact with
        // Database & File system.
        $command = $this->getMockBuilder('PHPCensor\\Command\\InstallCommand')
            ->setMethods([
                'reloadConfig',
                'verifyNotInstalled',
                'verifyDatabaseDetails',
                'setupDatabase',
                'createAdminUser',
                'createDefaultGroup',
                'writeConfigFile',
                'checkRequirements',
            ])->getMock();

        $self = $this;

        $command->expects($this->once())->method('verifyNotInstalled')->willReturn(true);
        $command->expects($this->once())->method('verifyDatabaseDetails')->willReturn(true);
        $command->expects($this->once())->method('setupDatabase')->willReturn(true);
        $command->expects($this->once())->method('createAdminUser')->will(
            $this->returnCallback(function ($adm) use ($self) {
                $self->admin = $adm;
            })
        );
        $command->expects($this->once())->method('writeConfigFile')->will(
            $this->returnCallback(function ($cfg) use ($self) {
                $self->config = $cfg;
            })
        );
        $command->expects($this->once())->method('checkRequirements');
        $command->expects($this->once())->method('createDefaultGroup');

        return $command;
    }

    protected function getCommandTester($helper)
    {
        $this->application->getHelperSet()->set($helper, 'question');
        $this->application->add($this->getInstallCommandMock());
        $command = $this->application->find('php-censor:install');
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function getConfig($exclude = null)
    {
        $config = [
            '--db-host'        => 'localhost',
            '--db-port'        => '3306',
            '--db-name'        => 'php-censor-db',
            '--db-user'        => 'php-censor-user',
            '--db-password'    => 'php-censor-password',
            '--db-type'        => 'mysql',
            '--admin-email'    => 'admin@php-censor.local',
            '--admin-name'     => 'admin',
            '--admin-password' => 'admin-password',
            '--url'            => 'http://php-censor.local',
            '--queue-use'      => false,
        ];

        if (!is_null($exclude)) {
          unset($config[$exclude]);
        }

        return $config;
    }

    protected function executeWithoutParam($param = null, $dialog)
    {
        // Clean result variables.
        $this->admin  = [];
        $this->config = [];

        // Get tester and execute with extracted parameters.
        $commandTester = $this->getCommandTester($dialog);
        $parameters    = $this->getConfig($param);
        $commandTester->execute($parameters);
    }

    public function testAutomaticInstallation()
    {
        $dialog = $this->getHelperMock();
        $dialog->expects($this->never())->method('ask');

        $this->executeWithoutParam(null, $dialog);
    }

    public function testDatabaseTypeConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-type', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->config['b8']['database']['type']);
    }

    public function testDatabaseHostConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-host', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->config['b8']['database']['servers']['read'][0]['host']);
        self::assertEquals('testedvalue', $this->config['b8']['database']['servers']['write'][0]['host']);
    }

    public function testDatabaseStringPortConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-port', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertArrayNotHasKey('port', $this->config['b8']['database']['servers']['read'][0]);
        self::assertArrayNotHasKey('port', $this->config['b8']['database']['servers']['write'][0]);
    }

    public function testDatabasePortConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('333');

        $this->executeWithoutParam('--db-port', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals(333, $this->config['b8']['database']['servers']['read'][0]['port']);
        self::assertEquals(333, $this->config['b8']['database']['servers']['write'][0]['port']);
    }

    public function testDatabaseNameConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-name', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->config['b8']['database']['name']);
    }

    public function testDatabaseUserConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-user', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->config['b8']['database']['username']);
    }

    public function testDatabasePasswordConfig()
    {
        $dialog = $this->getHelperMock();

        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--db-password', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->config['b8']['database']['password']);
    }

    public function testUrlConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('http://testedvalue.com');

        $this->executeWithoutParam('--url', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('http://testedvalue.com', $this->config['php-censor']['url']);
    }

    public function testAdminEmailConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('admin@php-censor.local');

        $this->executeWithoutParam('--admin-email', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('admin@php-censor.local', $this->admin['email']);
    }

    public function testAdminNameConfig()
    {
        $dialog = $this->getHelperMock();

        // Define expectation for dialog.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--admin-name', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->admin['name']);
    }

    public function testAdminPasswordConfig()
    {
        $dialog = $this->getHelperMock();

        // We specified an input value for hostname.
        $dialog->expects($this->once())->method('ask')->willReturn('testedvalue');

        $this->executeWithoutParam('--admin-password', $dialog);

        // Check that specified arguments are correctly loaded.
        self::assertEquals('testedvalue', $this->admin['password']);
    }
}
