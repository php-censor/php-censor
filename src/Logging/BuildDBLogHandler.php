<?php

declare(strict_types=1);

namespace PHPCensor\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PHPCensor\Model\Build;
use PHPCensor\Model\Secret;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\SecretStore;

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
    private SecretStore $secretStore;

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
        SecretStore $secretStore,
        BuildStore $buildStore,
        Build $build,
        int $level = Logger::INFO,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->secretStore = $secretStore;
        $this->build       = $build;
        $this->buildStore  = $buildStore;

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

    private function sanitize(string $message): string
    {
        return \str_replace([
            '\/',
            '//',
            $this->build->getBuildPath(),
            ROOT_DIR,
        ], [
            '/',
            '/',
            '<BUILD_PATH>/',
            '<PHP_CENSOR_PATH>/',
        ], $message);
    }

    private function sanitizeSecrets(string $message): string
    {
        $replace = [];
        $secrets = $this->secretStore->getAll();
        if (\count($secrets['items']) > 0) {
            /** @var Secret $secret */
            foreach ($secrets['items'] as $secret) {
                $value = $secret->getValue();
                $name  = '%' . \sprintf('SECRET:%s', $secret->getName()) . '%';
                if (\trim($value)) {
                    $replace[$name] = $secret->getValue();
                }
            }
        }

        return \str_replace($replace, \array_keys($replace), $message);
    }

    /**
     * Write a log entry to the build log.
     */
    protected function write(array $record): void
    {
        $this->logValue .= $this->sanitize(
            $this->sanitizeSecrets(
                (string)$record['message']
            )
        ) . PHP_EOL;

        if ($this->flushTimestamp < (\time() - $this->flushDelay)) {
            $this->flushData();
        }
    }
}
