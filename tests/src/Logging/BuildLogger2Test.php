<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Logging;

use PHPCensor\Logging\BuildLogger;
use PHPCensor\Model\Build;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\NullOutput;

class BuildLogger2Test extends TestCase
{
    public function testLog()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $build  = $this->createMock(Build::class);

        $buildLogger = new BuildLogger($logger, $build);
    }
}
