<?php

namespace Tests\PHPCensor\Plugin;

use b8\Config;
use PHPCensor\Plugin\Email as EmailPlugin;
use PHPCensor\Model\Build;

/**
 * Unit test for the PHPUnit plugin.
 *
 * @author meadsteve
 */
class EmailTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var EmailPlugin $testedPhpUnit
     */
    protected $testedEmailPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $mockBuilder
     */
    protected $mockBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $mockBuild
     */
    protected $mockBuild;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $mockProject
     */
    protected $mockProject;

    /**
     * @var int buildStatus
     */
    public $buildStatus;

    /**
     * @var array $message;
     */
    public $message;

    /**
     * @var bool $mailDelivered
     */
    public $mailDelivered;

    public function setUp()
    {
        $this->message       = [];
        $this->mailDelivered = true;
        $self                = $this;

        $config = new Config([
            'b8' => [
                'view' => [
                    'path' => SRC_DIR . 'View/'
                ]
            ]
        ]);

        $this->mockProject = $this
            ->getMockBuilder('\PHPCensor\Model\Project')
            ->setMethods(['getTitle'])
            ->setMockClassName('mockProject')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockProject->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue("Test-Project"));

        $this->mockBuild = $this
            ->getMockBuilder('\PHPCensor\Model\Build')
            ->setMethods(['getLog', 'getStatus', 'getProject', 'getCommitterEmail'])
            ->setMockClassName('mockBuild')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockBuild->expects($this->any())
            ->method('getLog')
            ->will($this->returnValue("Build Log"));

        $this->mockBuild->expects($this->any())
            ->method('getStatus')
            ->will($this->returnCallback(function () use ($self) {
                return $self->buildStatus;
            }));

        $this->mockBuild->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->mockProject));

        $this->mockBuild->expects($this->any())
            ->method('getCommitterEmail')
            ->will($this->returnValue('committer-email@example.com'));

        $this->mockBuilder = $this
            ->getMockBuilder('\PHPCensor\Builder')
            ->setMethods(['getSystemConfig', 'getBuild', 'log', 'logDebug'])
            ->setMockClassName('mockBuilder_email')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockBuilder->buildPath = "/";

        $this->mockBuilder->expects($this->any())
            ->method('getSystemConfig')
            ->with('php-censor')
            ->will($this->returnValue(['email_settings' => ['from_address' => "test-from-address@example.com"]]));
    }

    protected function loadEmailPluginWithOptions($arrOptions = [], $buildStatus = null, $mailDelivered = 1)
    {
        $this->mailDelivered = $mailDelivered;

        if (is_null($buildStatus)) {
            $this->buildStatus = Build::STATUS_SUCCESS;
        } else {
            $this->buildStatus = $buildStatus;
        }

        // Reset current message.
        $this->message = [];

        $self = $this;

        $this->testedEmailPlugin = $this
            ->getMockBuilder('\PHPCensor\Plugin\Email')
            ->setMethods(['sendEmail'])
            ->setConstructorArgs([$this->mockBuilder, $this->mockBuild, $arrOptions])
            ->getMock();

        $this->testedEmailPlugin->expects($this->any())
            ->method('sendEmail')
            ->will($this->returnCallback(function ($to, $cc, $subject, $body) use ($self) {
                $self->message['to'][]    = $to;
                $self->message['cc']      = $cc;
                $self->message['subject'] = $subject;
                $self->message['body']    = $body;

                return $self->mailDelivered;
            }));
    }

    public function testReturnsFalseWithoutArgs()
    {
        $this->loadEmailPluginWithOptions();

        $returnValue = $this->testedEmailPlugin->execute();

        // As no addresses will have been mailed as non are configured.
        $expectedReturn = false;

        self::assertEquals($expectedReturn, $returnValue);
    }

    public function testBuildsBasicEmails()
    {
        $this->loadEmailPluginWithOptions(['addresses' => ['test-receiver@example.com']], Build::STATUS_SUCCESS);

        $this->testedEmailPlugin->execute();

        self::assertContains('test-receiver@example.com', $this->message['to']);
    }

    public function testBuildsDefaultEmails()
    {
        $this->loadEmailPluginWithOptions(['default_mailto_address' => 'default-mailto-address@example.com'], Build::STATUS_SUCCESS);

        $this->testedEmailPlugin->execute();

        self::assertContains('default-mailto-address@example.com', $this->message['to']);
    }

    public function testExecute_UniqueRecipientsFromWithCommitter()
    {
        $this->loadEmailPluginWithOptions(['addresses' => ['test-receiver@example.com', 'test-receiver2@example.com']]);

        $returnValue = $this->testedEmailPlugin->execute();
        self::assertTrue($returnValue);

        self::assertCount(2, $this->message['to']);

        self::assertContains('test-receiver@example.com', $this->message['to']);
        self::assertContains('test-receiver2@example.com', $this->message['to']);
    }

    public function testExecute_UniqueRecipientsWithCommitter()
    {
        $this->loadEmailPluginWithOptions([
            'committer'  => true,
            'addresses'  => ['test-receiver@example.com', 'committer@test.com']
        ]);

        $returnValue = $this->testedEmailPlugin->execute();
        self::assertTrue($returnValue);

        self::assertContains('test-receiver@example.com', $this->message['to']);
        self::assertContains('committer@test.com', $this->message['to']);
    }

    public function testCcDefaultEmails()
    {
        $this->loadEmailPluginWithOptions(
            [
                'default_mailto_address' => 'default-mailto-address@example.com',
                'cc' => [
                    'cc-email-1@example.com',
                    'cc-email-2@example.com',
                    'cc-email-3@example.com',
                ],
            ],
            Build::STATUS_SUCCESS
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

    public function testBuildsCommitterEmails()
    {
        $this->loadEmailPluginWithOptions(
            [
                'committer' => true
            ],
            Build::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('committer-email@example.com', $this->message['to']);
    }

    public function testMailSuccessfulBuildHaveProjectName()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('Test-Project', $this->message['subject']);
        self::assertContains('Test-Project', $this->message['body']);
    }

    public function testMailFailingBuildHaveProjectName()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_FAILED
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('Test-Project', $this->message['subject']);
        self::assertContains('Test-Project', $this->message['body']);
    }

    public function testMailSuccessfulBuildHaveStatus()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_SUCCESS
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('Passing', $this->message['subject']);
        self::assertContains('success', $this->message['body']);
    }

    public function testMailFailingBuildHaveStatus()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_FAILED
        );

        $this->testedEmailPlugin->execute();

        self::assertContains('Failing', $this->message['subject']);
        self::assertContains('failed', $this->message['body']);
    }

    public function testMailDeliverySuccess()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_FAILED,
            1
        );

        $returnValue = $this->testedEmailPlugin->execute();

        self::assertEquals(true, $returnValue);
    }

    public function testMailDeliveryFail()
    {
        $this->loadEmailPluginWithOptions(
            [
                'addresses' => ['test-receiver@example.com']
            ],
            Build::STATUS_FAILED,
            0
        );

        $returnValue = $this->testedEmailPlugin->execute();

        self::assertEquals(false, $returnValue);
    }
}
