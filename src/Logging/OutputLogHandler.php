<?php

declare(strict_types=1);

namespace PHPCensor\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OutputLogHandler outputs the build log to the terminal.
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class OutputLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        protected OutputInterface $output,
        string $level = LogLevel::INFO,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * Write a log entry to the terminal.
     */
    protected function write(array $record): void
    {
        $this->output->write((string)$record['formatted']);
    }
}
