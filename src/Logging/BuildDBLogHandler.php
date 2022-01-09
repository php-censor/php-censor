<?php

declare(strict_types=1);

namespace PHPCensor\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PHPCensor\Model\Build;
use PHPCensor\Store\BuildStore;

/**
 * Class BuildDBLogHandler writes the build log to the database.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildDBLogHandler extends AbstractProcessingHandler
{
    protected Build $build;

    protected BuildStore $buildStore;

    protected string $logValue;

    /**
     * @var int last flush timestamp
     */
    protected int $flushTimestamp = 0;

    /**
     * @var int flush delay, seconds
     */
    protected int $flushDelay = 1;

    public function __construct(
        BuildStore $buildStore,
        Build $build,
        int $level = Logger::INFO,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->build      = $build;
        $this->buildStore = $buildStore;

        // We want to add to any existing saved log information.
        $this->logValue = (string)$build->getLog();
    }

    public function __destruct()
    {
        $this->flushData();
    }

    /**
     * Flush buffered data
     */
    protected function flushData(): void
    {
        $this->build->setLog($this->logValue);
        $this->buildStore->save($this->build);

        $this->flushTimestamp = \time();
    }

    /**
     * Write a log entry to the build log.
     */
    protected function write(array $record): void
    {
        $message = (string)$record['message'];
        $message = \str_replace(['\/', '//'], '/', $message);
        $message = \str_replace($this->build->getBuildPath(), '<BUILD_PATH>/', $message);
        $message = \str_replace(ROOT_DIR, '<PHP_CENSOR_PATH>/', $message);

        $this->logValue .= $message . PHP_EOL;

        if ($this->flushTimestamp < (\time() - $this->flushDelay)) {
            $this->flushData();
        }
    }
}
