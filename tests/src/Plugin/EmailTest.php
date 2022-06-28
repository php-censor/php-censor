<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Helper\VariableInterpolator;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Plugin;
use PHPCensor\Plugin\EmailNotify as EmailPlugin;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author meadsteve
 */
class EmailTest extends TestCase
{
    private EmailPlugin $testedEmailPlugin;
    private Builder $builder;
    private Build $build;
    private Project $project;
    private int $buildStatus;
    private array $message;
    private bool $mailDelivered;
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $this->message       = [];
        $this->mailDelivered = true;
        $self                = $this;

        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $this->project = $this
            ->getMockBuilder(Project::class)
            ->onlyMethods(['getTitle'])
            ->setMockClassName('mockProject')
            ->disableOriginalConstructor()
            ->getMock();

        $this->project->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue("Test-Project"));

        $this->build = $this
            ->getMockBuilder(Build::class)
            ->onlyMethods(['getLog', 'getStatus', 'getProject', 'getCommitterEmail'])
            ->setMockClassName('mockBuild')
            ->disableOriginalConstructor()
            ->getMock();

        $this->build->expects($this->any())
            ->method('getLog')
            ->will($this->returnValue("Build Log"));

        $this->build->expects($this->any())
            ->method('getStatus')
            ->will($this->returnCallback(function () use ($self) {
                return $self->buildStatus;
            }));

        $this->build->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->project));

        $this->build->expects($this->any())
            ->method('getCommitterEmail')
            ->will($this->returnValue('committer-email@example.com'));

        $this->build
            ->method('getProject')
            ->willReturn(new Project($this->storeRegistry));

        $this->builder = $this
            ->getMockBuilder(Builder::class)
            ->onlyMethods(['logNormal', 'logDebug', 'interpolate'])
            ->setMockClassName('mockBuilder_email')
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder->buildPath = "/";

        $secretStore = $this
            ->getMockBuilder(SecretStore::class)
            ->setConstructorArgs([$databaseManager, $this->storeRegistry])
            ->getMock();

        $environmentStore = $this
            ->getMockBuilder(EnvironmentStore::class)
            ->setConstructorArgs([$databaseManager, $this->storeRegistry])
            ->getMock();

        $interpolator = new VariableInterpolator(
            $this->build,
            $this->build->getProject(),
            $environmentStore,
            $secretStore,
            '1.0.0'
        );

        $this->builder->expects($this->any())
            ->method('interpolate')
            ->will($this->returnCallback(function () use ($self, $interpolator) {
                return $interpolator->interpolate("test");
            }));

        $configuration->expects($this->any())
            ->method('get')
            ->with('php-censor')
            ->will($this->returnValue(['email_settings' => ['from_address' => "test-from-address@example.com"]]));
    }

    protected function loadEmailPluginWithOptions(array $arrOptions = [], ?int $buildStatus = null, ?bool $mailDelivered = true): void
    {
        $this->mailDelivered = $mailDelivered;

        if (\is_null($buildStatus)) {
            $this->buildStatus = BuildInterface::STATUS_SUCCESS;
        } else {
            $this->buildStatus = $buildStatus;
        }

        // Reset current message.
        $this->message = [];

        $self = $this;

        $this->testedEmailPlugin = $this
            ->getMockBuilder(Plugin\EmailNotify::class)
            ->onlyMethods(['sendEmail'])
            ->setConstructorArgs([$this->builder, $this->build, $arrOptions])
            ->getMock();

        $this->testedEmailPlugin
            ->expects($this->any())
            ->method('sendEmail')
            ->will($this->returnCallback(function ($to, $cc, $subject, $body) use ($self) {
                $self->message['to'][]    = $to;
                $self->message['cc']      = $cc;
                $self->message['subject'] = $subject;
                $self->message['body']    = $body;

                return $self->mailDelivered;
            }));
    }

    public function testReturnsFalseWithoutArgs(): void
    {
        $this->loadEmailPluginWithOptions();

        $returnValue = $this->testedEmailPlugin->execute();

        // As no addresses will have been mailed as non are configured.
        $expectedReturn = false;

        self::assertEquals($expectedReturn, $returnValue);
    }

    public function testBuildsBasicEmails(): void
    {
        $this->loadEmailPluginWithOptions([
            'addresses' => ['test-receiver@example.com'],
        ], BuildInterface::STATUS_SUCCESS);

        $this->testedEmailPlugin->execute();

        self::assertContains('test-receiver@example.com', $this->message['to']);
    }

    public function testBuildsDefaultEmails(): void
    {
        $this->loadEmailPluginWithOptions([
            'default_mailto_address' => 'default-mailto-address@example.com',
        ], BuildInterface::STATUS_SUCCESS);

        $this->testedEmailPlugin->execute();

        self::assertContains('default-mailto-address@example.com', $this->message['to']);
    }

    public function testExecute_UniqueRecipientsFromWithCommitter(): void
    {
        $this->loadEmailPluginWithOptions([
            'addresses' => ['test-receiver@example.com', 'test-receiver2@example.com'],
        ]);

        $returnValue = $this->testedEmailPlugin->execute();
        self::assertTrue($returnValue);

        self::assertCount(2, $this->message['to']);

        self::assertContains('test-receiver@example.com', $this->message['to']);
        self::assertContains('test-receiver2@example.com', $this->message['to']);
    }

    public function testExecute_UniqueRecipientsWithCommitter(): void
    {
        $this->loadEmailPluginWithOptions([
            'committer' => true,
            'addresses' => ['test-receiver@example.com', 'committer@test.com'],
        ]);

        $returnValue = $this->testedEmailPlugin->execute();
        self::assertTrue($returnValue);

        self::assertContains('test-receiver@example.com', $this->message['to']);
        self::assertContains('committer@test.com', $this->message['to']);
    }

    public function testCcDefaultEmails(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'default_mailto_address' => 'default-mailto-address@example.com',
                'cc'                     => [
                    'cc-email-1@example.com',
                    'cc-email-2@example.com',
                    'cc-email-3@example.com',
                ],
            ],
            BuildInterface::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertEquals(
            [
                'cc-email-1@example.com',
                'cc-email-2@example.com',
                'cc-email-3@example.com',
            ],
            $this->message['cc']
        );
    }

    public function testBuildsCommitterEmails(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'committer' => true,
            ],
            BuildInterface::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('committer-email@example.com', $this->message['to']);
    }

    public function testMailSuccessfulBuildHaveProjectName(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com'],
            ],
            BuildInterface::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertStringContainsString('Test-Project', $this->message['subject']);
        self::assertStringContainsString('Test-Project', $this->message['body']);
    }

    public function testMailFailingBuildHaveProjectName(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com'],
            ],
            BuildInterface::STATUS_FAILED
        );

        $this->testedEmailPlugin->execute();

        self::assertStringContainsString('Test-Project', $this->message['subject']);
        self::assertStringContainsString('Test-Project', $this->message['body']);
    }

    public function testMailSuccessfulBuildHaveStatus(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com'],
            ],
            BuildInterface::STATUS_SUCCESS
        );

        self::assertEquals('local', $this->testedEmailPlugin->getPriorityPath());

        $this->testedEmailPlugin->execute();

        self::assertStringContainsString('Passing', $this->message['subject']);
        self::assertStringContainsString('success', $this->message['body']);
    }

    public function testMailFailingBuildHaveStatus(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses'     => ['test-receiver@example.com'],
                'priority_path' => 'global',
            ],
            BuildInterface::STATUS_FAILED
        );

        self::assertEquals('global', $this->testedEmailPlugin->getPriorityPath());

        $this->testedEmailPlugin->execute();

        self::assertStringContainsString('Failing', $this->message['subject']);
        self::assertStringContainsString('failed', $this->message['body']);
    }

    public function testMailDeliverySuccess(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses'     => ['test-receiver@example.com'],
                'priority_path' => 'system',
            ],
            BuildInterface::STATUS_FAILED,
            true
        );

        self::assertEquals('system', $this->testedEmailPlugin->getPriorityPath());

        $returnValue = $this->testedEmailPlugin->execute();

        self::assertEquals(true, $returnValue);
    }

    public function testMailDeliveryFail(): void
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses'     => ['test-receiver@example.com'],
                'priority_path' => 'Global',
            ],
            BuildInterface::STATUS_FAILED,
            false
        );

        self::assertEquals('local', $this->testedEmailPlugin->getPriorityPath());

        $returnValue = $this->testedEmailPlugin->execute();

        self::assertEquals(false, $returnValue);

        self::assertEquals('', Plugin::pluginName());
    }
}
