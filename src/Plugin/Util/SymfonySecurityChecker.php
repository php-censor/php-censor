<?php

namespace PHPCensor\Plugin\Util;

use PHPCensor\Plugin;

/**
 * Check security with "symfony security:check"
 *
 * Class is compatible to SensionLabs\Security\SecurityChecker
 */
class SymfonySecurityChecker
{
    /**
     * @var Plugin
     */
    private $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Checks a composer.lock file.
     *
     * @param string $lock The path to the composer.lock file
     *
     * @return string vulnerabilities as json
     *
     * @throws \RuntimeException When the lock file does not exist
     */
    public function check($lock)
    {
        if (!is_file($lock)) {
            throw new \RuntimeException('Lock file does not exist.');
        }

        $cmd = '%s check:security --format=json --dir=%s';
        $executable = $this->plugin->findBinary('symfony');
        $builder = $this->plugin->getBuilder();
        if (!$this->plugin->getBuild()->isDebug()) {
            $builder->logExecOutput(false);
        }

        // works with dir, composer.lock, composer.json
        $builder->executeCommand($cmd, $executable, $lock);

        $builder->logExecOutput(true);
        $output = $builder->getLastOutput();

        return $output;
    }
}
