<?php

namespace PHPCensor\Command;

use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use PHPCensor\Logging\OutputLogHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LoggingCommand extends Command
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Monolog\Logger $logger
     * @param string          $name
     */
    public function __construct(Logger $logger, $name = null)
    {
        parent::__construct($name);

        $this->logger = $logger;
    }

    /**
     * @param OutputInterface $output
     */
    protected function configureLogging(OutputInterface $output)
    {
        $level = Logger::ERROR;
        if ($output->isDebug()) {
            $level = Logger::DEBUG;
        } elseif ($output->isVeryVerbose()) {
            $level = Logger::INFO;
        } elseif ($output->isVerbose()) {
            $level = Logger::NOTICE;
        }

        $handlers = $this->logger->getHandlers();
        foreach ($handlers as $handler) {
            if ($handler instanceof AbstractHandler) {
                $handler->setLevel($level);
            }
        }

        if (!$output->isQuiet()) {
            $this->logger->pushHandler(
                new OutputLogHandler($output, $level)
            );
        }

        if ($output->isDebug()) {
            $this->logger->notice(
                sprintf('Command "%s" started in debug mode (-vvv).', $this->getName())
            );

            define('DEBUG_MODE', true);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureLogging($output);
    }
}
