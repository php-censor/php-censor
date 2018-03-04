<?php

namespace Tests\PHPCensor\Helper;

use PHPCensor\Helper\CommandExecutor;

class CommandExecutorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CommandExecutor
     */
    protected $testedExecutor;

    protected function setUp()
    {
        parent::setUp();

        $mockBuildLogger = $this->prophesize('PHPCensor\Logging\BuildLogger');

        $class = 'PHPCensor\Helper\CommandExecutor';
        $this->testedExecutor = new $class($mockBuildLogger->reveal(), __DIR__);
    }

    public function testGetLastOutput_ReturnsOutputOfCommand()
    {
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);
        $output = $this->testedExecutor->getLastOutput();
        self::assertEquals("Hello World", $output);
    }

    public function testGetLastOutput_ForgetsPreviousCommandOutput()
    {
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);
        $this->testedExecutor->executeCommand(['echo "%s"', 'Hello Tester']);
        $output = $this->testedExecutor->getLastOutput();
        self::assertEquals("Hello Tester", $output);
    }

    public function testExecuteCommand_ReturnsTrueForValidCommands()
    {
        $returnValue = $this->testedExecutor->executeCommand(['echo "%s"', 'Hello World']);
        self::assertTrue($returnValue);
    }

    public function testExecuteCommand_ReturnsFalseForInvalidCommands()
    {
        $returnValue = $this->testedExecutor->executeCommand(['eerfdcvcho "%s" > /dev/null 2>&1', 'Hello World']);
        self::assertFalse($returnValue);
    }

    /**
     * Runs a script that generates an output that fills the standard error
     * buffer first, followed by the standard output buffer. The function
     * should be able to read from both streams, thereby preventing the child
     * process from blocking because one of its buffers is full.
     */
    public function testExecuteCommand_AlternatesBothBuffers()
    {
        $length = 80000;
        $script = <<<EOD
/bin/sh -c 'data="$(printf %%${length}s | tr " " "-")"; >&2 echo "\$data"; >&1 echo "\$data"'
EOD;
        $data = str_repeat("-", $length);
        $returnValue = $this->testedExecutor->executeCommand([$script]);
        self::assertTrue($returnValue);
        self::assertEquals($data, trim($this->testedExecutor->getLastOutput()));
        self::assertEquals($data, trim($this->testedExecutor->getLastError()));
    }

    /**
     * @expectedException \Exception
     * @expectedMessageRegex WorldWidePeace
     */
    public function testFindBinary_ThrowsWhenNotFound()
    {
        $thisFileName = "WorldWidePeace";
        $this->testedExecutor->findBinary($thisFileName);
    }

    public function testFindBinary_ReturnsNullWihQuietArgument()
    {
        $thisFileName = "WorldWidePeace";
        self::assertFalse($this->testedExecutor->findBinary($thisFileName, true));
    }

    public function testReplaceIllegalCharacters()
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
