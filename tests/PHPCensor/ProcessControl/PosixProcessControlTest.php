<?php
namespace Tests\PHPCensor\ProcessControl;

use PHPCensor\ProcessControl\PosixProcessControl;

class PosixProcessControlTest extends UnixProcessControlTest
{
    protected function setUp()
    {
        $this->object = new PosixProcessControl();
    }

    public function testIsAvailable()
    {
        self::assertEquals(function_exists('posix_kill'), PosixProcessControl::isAvailable());
    }
}
