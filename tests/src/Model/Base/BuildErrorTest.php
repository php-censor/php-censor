<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Model;
use PHPCensor\Model\Base\BuildError;
use PHPUnit\Framework\TestCase;

class BuildErrorTest extends TestCase
{
    public function testConstruct(): void
    {
        $buildError = new BuildError();

        self::assertInstanceOf(Model::class, $buildError);
        self::assertInstanceOf(BuildError::class, $buildError);

        self::assertEquals([
            'id'          => null,
            'build_id'    => null,
            'plugin'      => null,
            'file'        => null,
            'line_start'  => null,
            'line_end'    => null,
            'severity'    => null,
            'message'     => null,
            'create_date' => null,
            'hash'        => null,
            'is_new'      => null,
        ], $buildError->getDataArray());
    }

    public function testId(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $buildError->getId());

        $result = $buildError->setId(100);
        self::assertEquals(false, $result);
    }

    public function testBuildId(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setBuildId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $buildError->getBuildId());

        $result = $buildError->setBuildId(200);
        self::assertEquals(false, $result);
    }

    public function testPlugin(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setPlugin('plugin');
        self::assertEquals(true, $result);
        self::assertEquals('plugin', $buildError->getPlugin());

        $result = $buildError->setPlugin('plugin');
        self::assertEquals(false, $result);
    }

    public function testFile(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setFile('file.php');
        self::assertEquals(true, $result);
        self::assertEquals('file.php', $buildError->getFile());

        $result = $buildError->setFile('file.php');
        self::assertEquals(false, $result);
    }

    public function testLineStart(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setLineStart(5);
        self::assertEquals(true, $result);
        self::assertEquals(5, $buildError->getLineStart());

        $result = $buildError->setLineStart(5);
        self::assertEquals(false, $result);
    }

    public function testLineEnd(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setLineEnd(10);
        self::assertEquals(true, $result);
        self::assertEquals(10, $buildError->getLineEnd());

        $result = $buildError->setLineEnd(10);
        self::assertEquals(false, $result);
    }

    public function testSeverity(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setSeverity(BuildError::SEVERITY_CRITICAL);
        self::assertEquals(true, $result);
        self::assertEquals(BuildError::SEVERITY_CRITICAL, $buildError->getSeverity());

        $result = $buildError->setSeverity(BuildError::SEVERITY_CRITICAL);
        self::assertEquals(false, $result);
    }

    public function testMessage(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setMessage('message');
        self::assertEquals(true, $result);
        self::assertEquals('message', $buildError->getMessage());

        $result = $buildError->setMessage('message');
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $buildError = new BuildError();
        self::assertEquals(null, $buildError->getCreateDate());

        $buildError = new BuildError();
        $createDate = new DateTime();

        $result = $buildError->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $buildError->getCreateDate()->getTimestamp());

        $result = $buildError->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $buildError = new BuildError(['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $buildError->getCreateDate()->getTimestamp());

        $buildError = new BuildError(['create_date' => 'Invalid Data']);
        self::assertNull($buildError->getCreateDate());
    }


    public function testHash(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setHash('hash');
        self::assertEquals(true, $result);
        self::assertEquals('hash', $buildError->getHash());

        $result = $buildError->setHash('hash');
        self::assertEquals(false, $result);
    }

    public function testIsNew(): void
    {
        $buildError = new BuildError();

        $result = $buildError->setIsNew(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $buildError->getIsNew());

        $result = $buildError->setIsNew(true);
        self::assertEquals(false, $result);
    }
}
