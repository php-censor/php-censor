<?php

namespace PHPCensor\Model\Build;

use GuzzleHttp\Client;
use PHPCensor\Builder;
use PHPCensor\Helper\Bitbucket;
use PHPCensor\Helper\Diff;
use b8\Config;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;

/**
 * BitBucket Build Model
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BitbucketBuild extends RemoteGitBuild
{
    /**
     * Get link to commit from another source (i.e. BitBucket)
     */
    public function getCommitLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/commits/' . $this->getCommitId();
    }

    /**
     * Get link to branch from another source (i.e. BitBucket)
     */
    public function getBranchLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/src/?at=' . $this->getBranch();
    }

    /**
     * Get link to tag from another source (i.e. BitBucket)
     */
    public function getTagLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/src/?at=' . $this->getTag();
    }

    /**
     * Send status updates to any relevant third parties (i.e. Bitbucket)
     *
     * @return bool
     */
    public function sendStatusPostback()
    {
        if (Build::SOURCE_WEBHOOK !== $this->getSource()) {
            return false;
        }

        $project = $this->getProject();
        if (empty($project)) {
            return false;
        }

        $username    = Config::getInstance()->get('php-censor.bitbucket.username');
        $appPassword = Config::getInstance()->get('php-censor.bitbucket.app_password');

        if (empty($username) || empty($appPassword) || empty($this->data['id'])) {
            return false;
        }

        switch ($this->getStatus()) {
            case 0:
            case 1:
                $status = 'INPROGRESS';
                $description = 'PHP Censor build running.';
                break;
            case 2:
                $status = 'SUCCESSFUL';
                $description = 'PHP Censor build passed.';
                break;
            case 3:
                $status = 'FAILED';
                $description = 'PHP Censor build failed.';
                break;
            default:
                $status = 'STOPPED';
                $description = 'PHP Censor build failed to complete.';
                break;
        }

        $phpCensorUrl = Config::getInstance()->get('php-censor.url');

        $url    = sprintf(
            '/2.0/repositories/%s/commit/%s/statuses/build',
            $this->getExtra('build_type') == 'pull_request'
                ? $this->getExtra('remote_reference')
                : $project->getReference(),
            $this->getCommitId()
        );

        $client = new Client([
            'base_uri' => 'https://api.bitbucket.org',
            'http_errors' => false,
        ]);
        $response = $client->post($url, [
            'auth'      => [$username, $appPassword],
            'headers'   => [
                'Content-Type' => 'application/json',
            ],
            'json'      => [
                'state'       => $status,
                'key'         => 'PHP-CENSOR',
                'url'         => $phpCensorUrl . '/build/view/' . $this->getId(),
                'name'        => 'PHP Censor Build #' . $this->getId(),
                'description' => $description,
            ],
        ]);

        $status = (integer)$response->getStatusCode();

        return ($status >= 200 && $status < 300);
    }

    /**
     * Get the URL to be used to clone this remote repository.
     */
    protected function getCloneUrl()
    {
        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            return 'git@bitbucket.org:' . $this->getProject()->getReference() . '.git';
        } else {
            return 'https://bitbucket.org/' . $this->getProject()->getReference() . '.git';
        }
    }

    /**
     * Get a template to use for generating links to files.
     *
     * @return string
     */
    public function getFileLinkTemplate()
    {
        $reference = $this->getProject()->getReference();

        if ($this->getExtra('build_type') == 'pull_request') {
            $reference = $this->getExtra('remote_reference');
        }

        $link = 'https://bitbucket.org/' . $reference . '/';
        $link .= 'src/' . $this->getCommitId() . '/';
        $link .= '{FILE}';
        $link .= '#{BASEFILE}-{LINE}';

        return $link;
    }

    /**
     * Handle any post-clone tasks, like applying a pull request patch on top of the branch.
     * @param Builder $builder
     * @param $cloneTo
     * @param array $extra
     * @return bool
     */
    protected function postCloneSetup(Builder $builder, $cloneTo, array $extra = null)
    {
        $buildType = $this->getExtra('build_type');

        $success = true;
        $skipGitFinalization = false;

        try {
            if (!empty($buildType) && $buildType == 'pull_request') {
                $helper = new Bitbucket();
                $diff = $helper->getPullRequestDiff(
                    $this->getProject()->getReference(),
                    $this->getExtra('pull_request_number')
                );

                $diffFile = $this->writeDiff($builder->buildPath, $diff);

                $cmd = 'cd "%s" && git checkout -b php-censor/' . $this->getId() . ' && git apply "%s"';

                $success = $builder->executeCommand($cmd, $cloneTo, $diffFile);

                unlink($diffFile);
                $skipGitFinalization = true;
            }
        } catch (\Exception $ex) {
            $success = false;
        }

        if ($success && !$skipGitFinalization) {
            $success = parent::postCloneSetup($builder, $cloneTo, $extra);
        }

        return $success;
    }

    /**
     * Create an diff file on disk for this build.
     *
     * @param  string $cloneTo
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

    /**
     * @inheritDoc
     */
    public function reportError(
        Builder $builder,
        $plugin,
        $message,
        $severity = BuildError::SEVERITY_NORMAL,
        $file = null,
        $lineStart = null,
        $lineEnd = null
    ) {
        $allowCommentCommit      = (boolean)Config::getInstance()->get('php-censor.bitbucket.comments.commit', false);
        $allowCommentPullRequest = (boolean)Config::getInstance()->get('php-censor.bitbucket.comments.pull_request', false);
        //$file = $builder->buildPath.'test.php';
        if ($allowCommentCommit || $allowCommentPullRequest) {
            $diffLineNumber = $this->getDiffLineNumber($builder, $file, $lineStart);

            if (!is_null($diffLineNumber)) {
                $helper = new Bitbucket();

                $repo     = $this->getProject()->getReference();
                $prNumber = $this->getExtra('pull_request_number');
                $commit   = $this->getCommitId();

                if (!empty($prNumber)) {
                    if ($allowCommentPullRequest) {
                        $helper->createPullRequestComment($repo, $prNumber, $commit, $file, $lineStart, $message);
                    }
                } else {
                    if ($allowCommentCommit) {
                        $helper->createCommitComment($repo, $commit, $file, $lineStart, $message);
                    }
                }
            }
        }

        parent::reportError($builder, $plugin, $message, $severity, $file, $lineStart, $lineEnd);
    }

    /**
     * Uses git diff to figure out what the diff line position is, based on the error line number.
     * @param Builder $builder
     * @param $file
     * @param $line
     * @return int|null
     */
    protected function getDiffLineNumber(Builder $builder, $file, $line)
    {
        $builder->logExecOutput(false);

        $line     = (integer)$line;
        $prNumber = $this->getExtra('pull_request_number');
        $path     = $builder->buildPath;

        if (!empty($prNumber)) {
            $builder->executeCommand('cd %s && git diff origin/%s "%s"', $path, $this->getBranch(), $file);
        } else {
            $commitId = $this->getCommitId();
            $compare  = empty($commitId) ? 'HEAD' : $commitId;

            $builder->executeCommand('cd %s && git diff %s^^ "%s"', $path, $compare, $file);
        }

        $builder->logExecOutput(true);

        $diff = $builder->getLastOutput();

        $helper = new Diff();
        $lines  = $helper->getLinePositions($diff);

        return isset($lines[$line]) ? $lines[$line] : null;
    }
}
