<?php

namespace PHPCensor\Model\Build;

use PHPCensor\Model\Build;
use PHPCensor\Builder;
use Psr\Log\LogLevel;

/**
 * Remote Git Build Model
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class GitBuild extends Build
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

        if ($success) {
            $success = $this->mergeBranches($builder, $buildPath);
        }

        if (!$success) {
            $builder->logFailure('Failed to clone remote git repository.');
            return false;
        }

        return $this->handleConfig($builder, $buildPath);
    }

    /**
     * @param Builder $builder
     * @param string $buildPath
     * @return bool
     */
    protected function mergeBranches(Builder $builder, $buildPath)
    {
        $branches = $this->getExtra('branches');
        if (!empty($branches)) {
            $cmd = 'cd "%s" && git merge --quiet origin/%s';
            foreach ($branches as $branch) {
                $success = $builder->executeCommand($cmd, $buildPath, $branch);
                if (!$success) {
                    $builder->log('Fail merge branch origin/'.$branch, LogLevel::ERROR);
                    return false;
                }
                $builder->log('Merged branch origin/'.$branch, LogLevel::INFO);
            }
        }
        return true;
    }

    /**
    * Use an HTTP-based git clone.
    */
    protected function cloneByHttp(Builder $builder, $cloneTo)
    {
        $cmd = 'cd .. && git clone --recursive ';

        $depth = $builder->getConfig('clone_depth');

        if (!is_null($depth)) {
            $cmd .= ' --depth ' . intval($depth) . ' ';
        }

        $cmd .= ' -b "%s" "%s" "%s"';
        $success = $builder->executeCommand($cmd, $this->getBranch(), $this->getCloneUrl(), $cloneTo);

        if ($success) {
            $success = $this->postCloneSetup($builder, $cloneTo);
        }

        return $success;
    }

    /**
    * Use an SSH-based git clone.
    */
    protected function cloneBySsh(Builder $builder, $cloneTo)
    {
        $keyFile       = $this->writeSshKey($cloneTo);
        $gitSshWrapper = $this->writeSshWrapper($cloneTo, $keyFile);

        // Do the git clone:
        $cmd = 'cd .. && git clone --recursive ';

        $depth = $builder->getConfig('clone_depth');

        if (!is_null($depth)) {
            $cmd .= ' --depth ' . intval($depth) . ' ';
        }

        $cmd .= ' -b "%s" "%s" "%s"';
        $cmd = 'export GIT_SSH="'.$gitSshWrapper.'" && ' . $cmd;

        $success = $builder->executeCommand($cmd, $this->getBranch(), $this->getCloneUrl(), $cloneTo);

        if ($success) {
            $extra = [
                'git_ssh_wrapper' => $gitSshWrapper
            ];

            $success = $this->postCloneSetup($builder, $cloneTo, $extra);
        }

        // Remove the key file and git wrapper:
        unlink($keyFile);
        unlink($gitSshWrapper);

        return $success;
    }

    /**
     * Handle any post-clone tasks, like switching branches.
     *
     * @param Builder $builder
     * @param string  $cloneTo
     * @param array   $extra
     *
     * @return boolean
     */
    protected function postCloneSetup(Builder $builder, $cloneTo, array $extra = null)
    {
        $success  = true;
        $commitId = $this->getCommitId();
        $chdir    = 'cd "%s"';

        if (empty($this->getEnvironment()) && !empty($commitId)) {
            $cmd     = $chdir . ' && git checkout %s --quiet';
            $success = $builder->executeCommand($cmd, $cloneTo, $commitId);
        }

        // Always update the commit hash with the actual HEAD hash
        if ($builder->executeCommand($chdir . ' && git rev-parse HEAD',  $cloneTo)) {
            $commitId = trim($builder->getLastOutput());

            $this->setCommitId($commitId);

            if ($builder->executeCommand($chdir . ' && git log -1 --pretty=format:%%s %s', $cloneTo, $commitId)) {
                $this->setCommitMessage(trim($builder->getLastOutput()));
            }

            if ($builder->executeCommand($chdir . ' && git log -1 --pretty=format:%%ae %s', $cloneTo, $commitId)) {
                $this->setCommitterEmail(trim($builder->getLastOutput()));
            }
        }

        return $success;
    }
}
