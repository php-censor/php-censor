<?php

namespace PHPCensor\Model\Build;

use Exception;
use PHPCensor\Builder;
use PHPCensor\Model\Build;

/**
 * Local Build Model
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class LocalBuild extends TypedBuild
{
    /**
     * Create a working copy by cloning, copying, or similar.
     *
     * @param string $buildPath
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createWorkingCopy(Builder $builder, $buildPath)
    {
        $reference  = $this->getProject()->getReference();
        $reference  = \substr($reference, -1) === '/' ? \substr($reference, 0, -1) : $reference;
        $buildPath  = \substr($buildPath, 0, -1);

        // If there's a /config file in the reference directory, it is probably a bare repository
        // which we'll extract into our build path directly.
        if (\is_file($reference . '/config') &&
            true === $this->handleBareRepository($builder, $reference, $buildPath)) {
            return $this->handleConfig($builder, $buildPath);
        }

        $configHandled = $this->handleConfig($builder, $reference);

        if ($configHandled === false) {
            return false;
        }

        $buildSettings = $builder->getConfig('build_settings');

        if (isset($buildSettings['prefer_symlink']) && $buildSettings['prefer_symlink'] === true) {
            return $this->handleSymlink($builder, $reference, $buildPath);
        } else {
            $cmd = 'mkdir -p "%s" && cp -Rf %s/* "%s/"';
            $builder->executeCommand($cmd, $buildPath, $reference, $buildPath);
        }

        return true;
    }

    /**
     * Check if this is a "bare" git repository, and if so, unarchive it.
     *
     * @param string $reference
     * @param string $buildPath
     *
     * @return bool
     */
    protected function handleBareRepository(Builder $builder, $reference, $buildPath)
    {
        $gitConfig = \parse_ini_file($reference.'/config', true);

        // If it is indeed a bare repository, then extract it into our build path:
        if ($gitConfig['core']['bare']) {
            $cmd = 'mkdir %2$s; git --git-dir="%1$s" archive %3$s | tar -x -C "%2$s"';
            $builder->executeCommand($cmd, $reference, $buildPath, $this->getBranch());

            return true;
        }

        return false;
    }

    /**
     * Create a symlink if required.
     *
     * @param string $reference
     * @param string $buildPath
     *
     * @return bool
     */
    protected function handleSymlink(Builder $builder, $reference, $buildPath)
    {
        if (\is_link($buildPath) && \is_file($buildPath)) {
            \unlink($buildPath);
        }

        $builder->logNormal(\sprintf('Symlinking: %s to %s', $reference, $buildPath));

        if (!\symlink($reference, $buildPath)) {
            $builder->logFailure('Failed to symlink.');

            return false;
        }

        return true;
    }
}
