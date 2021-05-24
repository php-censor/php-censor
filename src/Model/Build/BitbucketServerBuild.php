<?php

namespace PHPCensor\Model\Build;

use Exception;
use PHPCensor\Builder;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Model\Build;

class BitbucketServerBuild extends GitBuild
{
    /**
     * @var array
     */
    public static $pullrequestTriggersToSources = [
        'pr:opened'   => Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED,
        'pr:updated'  => Build::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED,
        'pr:approved' => Build::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED,
        'pr:merged'   => Build::SOURCE_WEBHOOK_PULL_REQUEST_MERGED,
    ];

    /**
     * Get link to commit from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getCommitLink()
    {
        return $this->getProject()->getReference() . '/commits/' . $this->getCommitId();
    }

    /**
     * Get link to branch from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getBranchLink()
    {
        return $this->getProject()->getReference() . '/src/?at=' . $this->getBranch();
    }

    /**
     * Get link to remote branch (from pull request) from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getRemoteBranchLink()
    {
        $remoteBranch    = $this->getExtra('remote_branch');
        $remoteReference = $this->getExtra('remote_reference');

        return $this->getProject()->getReference() . $remoteReference . '/src/?at=' . $remoteBranch;
    }

    /**
     * Get link to tag from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getTagLink()
    {
        return $this->getProject()->getReference() . '/src/?at=' . $this->getTag();
    }

    /**
     * Send status updates to any relevant third parties (i.e. Bitbucket)
     *
     * @return boolean
     */
    public function sendStatusPostback()
    {
        // Just do success.  Will have to build a webhook in
        // bitbucket to get this or install an app that we can format a request to.
        return true;
    }

    /**
     * Get the URL to be used to clone this remote repository.
     *
     * @return string
     */
    protected function getCloneUrl()
    {
        return $this->getProject()->getReference() . '.git';
    }

    /**
     * Get a template to use for generating links to files.
     *
     * @return string|null
     */
    public function getFileLinkTemplate()
    {
        $reference = $this->getProject()->getReference();

        if (in_array($this->getSource(), Build::$pullRequestSources, true)) {
            $reference = $this->getExtra('remote_reference');
        }

        $link = $this->getProject()->getReference() . $reference . '/';
        $link .= 'src/' . $this->getCommitId() . '/';
        $link .= '{FILE}';
        $link .= '#{BASEFILE}-{LINE}';

        return $link;
    }

    /**
     * @inheritdoc
     */
    protected function postCloneSetup(Builder $builder, $cloneTo, array $extra = null)
    {
        $success             = true;
        $skipGitFinalization = false;

        try {
            if (in_array($this->getSource(), Build::$pullRequestSources, true)) {
                $diff = $this->getPullRequestDiff($builder, $cloneTo, $extra['remote_branch']);

                $diffFile = $this->writeDiff($builder->buildPath, $diff);

                $cmd = 'cd "%s" && git checkout -b php-censor/' . $this->getId();

                $success = $builder->executeCommand($cmd, $cloneTo);

                if ($success) {
                    $applyCmd = 'git apply "%s"';
                    $success  = $builder->executeCommand($applyCmd, $diffFile);
                }

                //unlink($diffFile);
                $skipGitFinalization = true;
            }
        } catch (Exception $ex) {
            $success = false;
        }

        if ($success && !$skipGitFinalization) {
            $success = parent::postCloneSetup($builder, $cloneTo, $extra);
        }

        return $success;
    }

    /**
     * Create request patch with diff
     *
     * @param Builder $builder
     * @param string $cloneTo
     * @param string $targetBranch
     */
    protected function getPullRequestDiff(Builder $builder, $cloneTo, $targetBranch)
    {
        $cmd     = 'cd "%s" && git diff %s';
        $success = $builder->executeCommand($cmd, $cloneTo, $targetBranch);

        if ($success) {
            return $builder->getLastOutput();
        }

        throw new RuntimeException('Unable to create diff patch.');
    }

    /**
     * Create an diff file on disk for this build.
     *
     * @param string $cloneTo
     * @param string $diff
     *
     * @return string
     */
    protected function writeDiff($cloneTo, $diff)
    {
        $filePath = dirname($cloneTo . '/temp');
        $diffFile = $filePath . '.patch';

        file_put_contents($diffFile, $diff);
        chmod($diffFile, 0600);

        return $diffFile;
    }
}
