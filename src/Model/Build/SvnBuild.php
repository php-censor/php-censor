<?php

namespace PHPCensor\Model\Build;

use PHPCensor\Model\Build;
use PHPCensor\Builder;

/**
 * Remote Subversion Build Model
 *
 * @author Nadir Dzhilkibaev <imam.sharif@gmail.com>
 */
class SvnBuild extends Build
{
    protected $svnCommand = 'svn export -q --non-interactive ';

    /**
     * Get the URL to be used to clone this remote repository.
     */
    protected function getCloneUrl()
    {
        $url    = rtrim($this->getProject()->getReference(), '/') . '/';
        $branch = ltrim($this->getBranch(), '/');

        // For empty default branch or default branch name like "/trunk" or "trunk" (-> "trunk")
        if (empty($branch) || $branch == 'trunk') {
            $url .= 'trunk';
        // For default branch with standard default branch directory ("branches") like "/branch-1" or "branch-1"
        // (-> "branches/branch-1")
        } elseif (false === strpos($branch, '/')) {
            $url .= 'branches/' . $branch;
        // For default branch with non-standard branch directory like "/branch/branch-1" or "branch/branch-1"
        // (-> "branch/branch-1")
        } else {
            $url .= $branch;
        }

        return $url;
    }

    /**
     * @param Builder $builder
     *
     * @return void
     */
    protected function extendSvnCommandFromConfig(Builder $builder)
    {
        $cmd = $this->svnCommand;

        $svn = $builder->getConfig('svn');
        if ($svn) {
            foreach ($svn as $key => $value) {
                $cmd .= " --$key $value ";
            }
        }

        $depth = $builder->getConfig('clone_depth');

        if (!is_null($depth)) {
            $cmd .= ' --depth ' . intval($depth) . ' ';
        }

        $this->svnCommand = $cmd;
    }

    /**
     * Create a working copy by cloning, copying, or similar.
     */
    public function createWorkingCopy(Builder $builder, $buildPath)
    {
        $this->handleConfig($builder, $buildPath);

        $this->extendSvnCommandFromConfig($builder);

        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            $success = $this->cloneBySsh($builder, $buildPath);
        } else {
            $success = $this->cloneByHttp($builder, $buildPath);
        }

        if (!$success) {
            $builder->logFailure('Failed to export remote subversion repository.');
            return false;
        }

        return $this->handleConfig($builder, $buildPath);
    }

    /**
     * Use an HTTP-based svn export.
     */
    protected function cloneByHttp(Builder $builder, $cloneTo)
    {
        $cmd = $this->svnCommand;

        if (!empty($this->getCommitId())) {
            $cmd .= ' -r %s %s "%s"';
            $success = $builder->executeCommand($cmd, $this->getCommitId(), $this->getCloneUrl(), $cloneTo);
        } else {
            $cmd .= ' %s "%s"';
            $success = $builder->executeCommand($cmd, $this->getCloneUrl(), $cloneTo);
        }

        return $success;
    }

    /**
     * Use an SSH-based svn export.
     */
    protected function cloneBySsh(Builder $builder, $cloneTo)
    {
        $cmd        = $this->svnCommand . ' %s "%s"';
        $keyFile    = $this->writeSshKey($cloneTo);
        $sshWrapper = $this->writeSshWrapper($cloneTo, $keyFile);
        $cmd        = 'export SVN_SSH="' . $sshWrapper . '" && ' . $cmd;

        $success = $builder->executeCommand($cmd, $this->getCloneUrl(), $cloneTo);

        // Remove the key file and svn wrapper:
        unlink($keyFile);
        unlink($sshWrapper);

        return $success;
    }
}
