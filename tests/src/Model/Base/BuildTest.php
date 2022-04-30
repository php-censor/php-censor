<?php

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model\Base\Build;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class BuildTest extends TestCase
{
    protected StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct()
    {
        $build = new Build($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $build);
        self::assertInstanceOf('PHPCensor\Model\Base\Build', $build);

        self::assertEquals([
            'id'                     => null,
            'parent_id'              => null,
            'project_id'             => null,
            'commit_id'              => null,
            'status'                 => null,
            'log'                    => null,
            'branch'                 => null,
            'tag'                    => null,
            'create_date'            => null,
            'start_date'             => null,
            'finish_date'            => null,
            'committer_email'        => null,
            'commit_message'         => null,
            'extra'                  => [],
            'environment_id'         => null,
            'source'                 => Build::SOURCE_UNKNOWN,
            'user_id'                => null,
            'errors_total'           => null,
            'errors_total_previous'  => null,
            'errors_new'             => null,
            'test_coverage'          => null,
            'test_coverage_previous' => null,
        ], $build->getDataArray());
    }

    public function testId()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $build->getId());

        $result = $build->setId(100);
        self::assertEquals(false, $result);

        self::assertEquals(['id' => 'id'], $build->getModified());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testParentId()
    {
        $build = new Build($this->storeRegistry);

        self::assertEquals(0, $build->getParentId());

        $result = $build->setParentId(222);
        self::assertEquals(true, $result);
        self::assertEquals(222, $build->getParentId());

        $result = $build->setParentId(222);
        self::assertEquals(false, $result);
    }

    public function testProjectId()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setProjectId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $build->getProjectId());

        $result = $build->setProjectId(200);
        self::assertEquals(false, $result);
    }

    public function testCommitId()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setCommitId('commit');
        self::assertEquals(true, $result);
        self::assertEquals('commit', $build->getCommitId());

        $result = $build->setCommitId('commit');
        self::assertEquals(false, $result);
    }

    public function testStatus()
    {
        $build = new Build($this->storeRegistry);

        $build->setStatusFailed();
        self::assertEquals(Build::STATUS_FAILED, $build->getStatus());

        self::expectException('\PHPCensor\Common\Exception\InvalidArgumentException');
        $build->setStatus(10);
    }

    public function testLog()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setLog('log');
        self::assertEquals(true, $result);
        self::assertEquals('log', $build->getLog());

        $result = $build->setLog('log');
        self::assertEquals(false, $result);
    }

    public function testBranch()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setBranch('branch');
        self::assertEquals(true, $result);
        self::assertEquals('branch', $build->getBranch());

        $result = $build->setBranch('branch');
        self::assertEquals(false, $result);
    }

    public function testTag()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setTag('tag');
        self::assertEquals(true, $result);
        self::assertEquals('tag', $build->getTag());

        $result = $build->setTag('tag');
        self::assertEquals(false, $result);
    }

    public function testCreateDate()
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals(null, $build->getCreateDate());

        $build      = new Build($this->storeRegistry);
        $createDate = new DateTime();

        $result = $build->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $build->getCreateDate()->getTimestamp());

        $result = $build->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $build = new Build($this->storeRegistry, ['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $build->getCreateDate()->getTimestamp());

        $build = new Build($this->storeRegistry, ['create_date' => 'Invalid Data']);
        self::assertNull($build->getCreateDate());
    }

    public function testStartDate()
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals(null, $build->getStartDate());

        $build      = new Build($this->storeRegistry);
        $startDate = new DateTime();

        $result = $build->setStartDate($startDate);
        self::assertEquals(true, $result);
        self::assertEquals($startDate->getTimestamp(), $build->getStartDate()->getTimestamp());

        $result = $build->setStartDate($startDate);
        self::assertEquals(false, $result);

        $build = new Build($this->storeRegistry, ['start_date' => $startDate->format('Y-m-d H:i:s')]);
        self::assertEquals($startDate->getTimestamp(), $build->getStartDate()->getTimestamp());

        $build = new Build($this->storeRegistry, ['start_date' => 'Invalid Data']);
        self::assertNull($build->getStartDate());
    }

    public function testFinishDate()
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals(null, $build->getFinishDate());

        $build      = new Build($this->storeRegistry);
        $finishDate = new DateTime();

        $result = $build->setFinishDate($finishDate);
        self::assertEquals(true, $result);
        self::assertEquals($finishDate->getTimestamp(), $build->getFinishDate()->getTimestamp());

        $result = $build->setFinishDate($finishDate);
        self::assertEquals(false, $result);

        $build = new Build($this->storeRegistry, ['finish_date' => $finishDate->format('Y-m-d H:i:s')]);
        self::assertEquals($finishDate->getTimestamp(), $build->getFinishDate()->getTimestamp());

        $build = new Build($this->storeRegistry, ['finish_date' => 'Invalid Data']);
        self::assertNull($build->getStartDate());
    }

    public function testCommitterEmail()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setCommitterEmail('email@email.com');
        self::assertEquals(true, $result);
        self::assertEquals('email@email.com', $build->getCommitterEmail());

        $result = $build->setCommitterEmail('email@email.com');
        self::assertEquals(false, $result);
    }

    public function testCommitMessage()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setCommitMessage('message');
        self::assertEquals(true, $result);
        self::assertEquals('message', $build->getCommitMessage());

        $result = $build->setCommitMessage('message');
        self::assertEquals(false, $result);
    }

    public function testExtra()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setExtra(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $build->getExtra());
        self::assertEquals('value-1', $build->getExtra('key-1'));
        self::assertEquals(null, $build->getExtra('key-3'));

        $result = $build->setExtra(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(false, $result);
    }

    /*
     * @depends testExtra
     */
    public function testAddExtraValue()
    {
        $build = new Build($this->storeRegistry, ['extra' => null]);

        $result = $build->addExtraValue('key-1', 'value-1');
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1'], $build->getExtra());

        $result = $build->addExtraValue('key-2', 'value-2');
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $build->getExtra());

        $result = $build->addExtraValue('key-1', 'value-1');
        self::assertEquals(false, $result);
    }

    /*
     * @depends testExtra
     */
    public function testRemoveExtraValue()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->removeExtraValue('key-1', 'value-1');
        self::assertEquals(false, $result);

        $build = new Build($this->storeRegistry, [
            'extra' => [
                'key-1' => 'value-1',
                'key-2' => 'value-2'
            ]
        ]);

        $result = $build->removeExtraValue('key-1');
        self::assertEquals(true, $result);
        self::assertEquals(['key-2' => 'value-2'], $build->getExtra());

        $result = $build->removeExtraValue('key-1');
        self::assertEquals(false, $result);
    }

    public function testEnvironment()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setEnvironmentId(22);
        self::assertEquals(true, $result);
        self::assertEquals(22, $build->getEnvironmentId());

        $result = $build->setEnvironmentId(22);
        self::assertEquals(false, $result);
    }

    public function testSource()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setSource(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED);
        self::assertEquals(true, $result);
        self::assertEquals(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED, $build->getSource());

        $result = $build->setSource(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED);
        self::assertEquals(false, $result);

        self::expectException('\PHPCensor\Common\Exception\InvalidArgumentException');
        $build->setSource(20);
    }

    public function testUserId()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setUserId(300);
        self::assertEquals(true, $result);
        self::assertEquals(300, $build->getUserId());

        $result = $build->setUserId(300);
        self::assertEquals(false, $result);
    }

    public function testErrorsTotal()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setErrorsTotal(5);
        self::assertEquals(true, $result);
        self::assertEquals(5, $build->getErrorsTotal());

        $result = $build->setErrorsTotal(5);
        self::assertEquals(false, $result);
    }

    public function testErrorsTotalPrevious()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setErrorsTotalPrevious(5);
        self::assertEquals(true, $result);
        self::assertEquals(5, $build->getErrorsTotalPrevious());

        $result = $build->setErrorsTotalPrevious(5);
        self::assertEquals(false, $result);
    }

    public function testErrorsNew()
    {
        $build = new Build($this->storeRegistry);

        $result = $build->setErrorsNew(5);
        self::assertEquals(true, $result);
        self::assertEquals(5, $build->getErrorsNew());

        $result = $build->setErrorsNew(5);
        self::assertEquals(false, $result);
    }

    /*
     * @depends testExtra
     */
    public function testIsDebug()
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals(false, $build->isDebug());

        $build->addExtraValue('debug', true);
        self::assertEquals(true, $build->isDebug());

        $build->addExtraValue('debug', false);
        self::assertEquals(false, $build->isDebug());


        $build = new Build($this->storeRegistry);

        define('DEBUG_MODE', true);
        self::assertEquals(true, $build->isDebug());
    }
}
