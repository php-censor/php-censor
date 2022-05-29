<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use Exception;
use PHPCensor\Helper\CommandExecutor;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CommandExecutorTest extends TestCase
{
    use ProphecyTrait;

    protected CommandExecutor $testedExecutor;

    protected function setUp(): void
    {
        parent::setUp();

        $buildLogger = $this->prophesize('PHPCensor\Logging\BuildLogger');

        $class = 'PHPCensor\Helper\CommandExecutor';
        $this->testedExecutor = new $class($buildLogger->reveal(), __DIR__);
    }

    public function testGetLastOutput_ReturnsOutputOfCommand(): void
    {
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);
        $output = $this->testedExecutor->getLastOutput();

        self::assertEquals("Hello World", $output);
    }

    public function testGetLastOutput_ForgetsPreviousCommandOutput(): void
    {
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello Tester']);
        $output = $this->testedExecutor->getLastOutput();

        self::assertEquals("Hello Tester", $output);
    }

    public function testExecuteCommand_ReturnsTrueForValidCommands(): void
    {
        $returnValue = $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);

        self::assertTrue($returnValue);
    }

    public function testExecuteCommand_ReturnsFalseForInvalidCommands(): void
    {
        $returnValue = $this->testedExecutor->executeCommand(['eerfdcvcho "%s" > /dev/null 2>&1', 'Hello World']);

        self::assertFalse($returnValue);
    }

    public function testFindBinary_ThrowsWhenNotFound(): void
    {
        self::expectException(Exception::class);

        $thisFileName = "WorldWidePeace";
        $this->testedExecutor->findBinary($thisFileName);
    }

    public function testReplaceIllegalCharacters(): void
    {
        self::assertEquals(
            "start � end",
            $this->testedExecutor->replaceIllegalCharacters(
                "start \xf0\x9c\x83\x96 end"
            )
        );

        self::assertEquals(
            "start � end",
            $this->testedExecutor->replaceIllegalCharacters(
                "start \xF0\x9C\x83\x96 end"
            )
        );

        self::assertEquals(
            "start 123_X08�_X00�_Xa4�_5432 end",
            $this->testedExecutor->replaceIllegalCharacters(
                "start 123_X08\x08_X00\x00_Xa4\xa4_5432 end"
            )
        );
    }
}
