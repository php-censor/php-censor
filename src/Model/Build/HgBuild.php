<?php

namespace PHPCensor\Model\Build;

use PHPCensor\Model\Build;
use PHPCensor\Builder;

/**
 * Mercurial Build Model
 *
 * @author Pavel Gopanenko <pavelgopanenko@gmail.com>
 */
class HgBuild extends Build
{
    /**
     * Get the URL to be used to clone this remote repository.
     */
    protected function getCloneUrl()
    {
        return $this->getProject()->getReference();
    }

    /**
     * Create a working copy by cloning, copying, or similar.
     */
    public function createWorkingCopy(Builder $builder, $buildPath)
    {
        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            $success = $this->cloneBySsh($builder, $buildPath);
        } else {
            $success = $this->cloneByHttp($builder, $buildPath);
        }

        if (!$success) {
            $builder->logFailure('Failed to clone remote hg repository.');

            return false;
        }

        return $this->handleConfig($builder, $buildPath);
    }

    /**
     * Use a HTTP-based hg clone.
     */
    protected function cloneByHttp(Builder $builder, $cloneTo)
    {
        return $builder->executeCommand('hg clone %s "%s" -r %s', $this->getCloneUrl(), $cloneTo, $this->getBranch());
    }

    /**
     * Use an SSH-based hg clone.
     *
     * @param Builder $builder
     * @param string  $cloneTo
     *
     * @return bool
     */
    protected function cloneBySsh(Builder $builder, $cloneTo)
    {
        $keyFile = $this->writeSshKey($cloneTo);

        // Do the hg clone:
        $cmd     = 'hg clone --ssh "ssh -i '.$keyFile.'" %s "%s" -r %s';
        $success = $builder->executeCommand($cmd, $this->getCloneUrl(), $cloneTo, $this->getBranch());

        if ($success) {
            $success = $this->postCloneSetup($builder, $cloneTo);
        }

        // Remove the key file:
        unlink($keyFile);

        return $success;
    }

    /**
     * Handle post-clone tasks (switching branch, etc.)
     *
     * @param Builder $builder
     * @param string  $cloneTo
     * @param array   $extra
     *
     * @return bool
     */
    protected function postCloneSetup(Builder $builder, $cloneTo, array $extra = null)
    {
        $success  = true;
        $commitId = $this->getCommitId();

        // Allow switching to a specific branch:
        if (!empty($commitId)) {
            $cmd     = 'cd "%s" && hg checkout %s';
            $success = $builder->executeCommand($cmd, $cloneTo, $this->getBranch());
        }

        return $success;
    }
}
