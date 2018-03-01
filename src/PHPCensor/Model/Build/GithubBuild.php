<?php

namespace PHPCensor\Model\Build;

use GuzzleHttp\Client;
use PHPCensor\Builder;
use PHPCensor\Helper\Diff;
use PHPCensor\Helper\Github;
use b8\Config;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;

/**
 * Github Build Model
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class GithubBuild extends GitBuild
{
    /**
     * Get link to commit from another source (i.e. Github)
     *
     * @return string
     */
    public function getCommitLink()
    {
        return 'https://github.com/' . $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
    }

    /**
     * Get link to branch from another source (i.e. Github)
     *
     * @return string
     */
    public function getBranchLink()
    {
        return 'https://github.com/' . $this->getProject()->getReference() . '/tree/' . $this->getBranch();
    }

    /**
     * Get link to remote branch (from pull request) from another source (i.e. Github)
     */
    public function getRemoteBranchLink()
    {
        $remoteBranch    = $this->getExtra('remote_branch');
        $remoteReference = $this->getExtra('remote_reference');

        return 'https://github.com/' . $remoteReference . '/tree/' . $remoteBranch;
    }

    /**
     * Get link to tag from another source (i.e. Github)
     *
     * @return string
     */
    public function getTagLink()
    {
        return 'https://github.com/' . $this->getProject()->getReference() . '/tree/' . $this->getTag();
    }

    /**
     * Send status updates to any relevant third parties (i.e. Github)
     *
     * @return boolean
     */
    public function sendStatusPostback()
    {
        if (!in_array($this->getSource(), [Build::SOURCE_WEBHOOK, Build::SOURCE_WEBHOOK_PULL_REQUEST], true)) {
            return false;
        }

        $project = $this->getProject();
        if (empty($project)) {
            return false;
        }

        $token = Config::getInstance()->get('php-censor.github.token');
        if (empty($token) || empty($this->data['id'])) {
            return false;
        }

        $allowStatusCommit = (boolean)Config::getInstance()->get(
            'php-censor.github.status.commit',
            false
        );

        if (!$allowStatusCommit) {
            return false;
        }

        switch ($this->getStatus()) {
            case 0:
            case 1:
                $status = 'pending';
                $description = 'PHP Censor build running.';
                break;
            case 2:
                $status = 'success';
                $description = 'PHP Censor build passed.';
                break;
            case 3:
                $status = 'failure';
                $description = 'PHP Censor build failed.';
                break;
            default:
                $status = 'error';
                $description = 'PHP Censor build failed to complete.';
                break;
        }

        $phpCensorUrl = Config::getInstance()->get('php-censor.url');

        $url    = '/repos/' . $project->getReference() . '/statuses/' . $this->getCommitId();
        $client = new Client([
            'base_uri'    => 'https://api.github.com',
            'http_errors' => false,
        ]);
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'token ' . $token,
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'json' => [
                'state'       => $status,
                'target_url'  => $phpCensorUrl . '/build/view/' . $this->getId(),
                'description' => $description,
                'context'     => 'PHP Censor',
            ]
        ]);

        $status = (integer)$response->getStatusCode();

        return ($status >= 200 && $status < 300);
    }

    /**
     * Get the URL to be used to clone this remote repository.
     *
     * @return string
     */
    protected function getCloneUrl()
    {
        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            return 'git@github.com:' . $this->getProject()->getReference() . '.git';
        } else {
            return 'https://github.com/' . $this->getProject()->getReference() . '.git';
        }
    }

    /**
     * Get a parsed version of the commit message, with links to issues and commits.
     *
     * @return string
     */
    public function getCommitMessage()
    {
        $rtn = parent::getCommitMessage();

        $project = $this->getProject();

        if (!is_null($project)) {
            $reference = $project->getReference();
            $commitLink = '<a href="https://github.com/' . $reference . '/issues/$1">#$1</a>';
            $rtn = preg_replace('/\#([0-9]+)/', $commitLink, $rtn);
            $rtn = preg_replace('/\@([a-zA-Z0-9_]+)/', '<a href="https://github.com/$1">@$1</a>', $rtn);
        }

        return $rtn;
    }

    /**
     * Get a template to use for generating links to files.
     *
     * @return string
     */
    public function getFileLinkTemplate()
    {
        $reference = $this->getProject()->getReference();

        if (Build::SOURCE_WEBHOOK_PULL_REQUEST === $this->getSource()) {
            $reference = $this->getExtra('remote_reference');
        }

        $link = 'https://github.com/' . $reference . '/';
        $link .= 'blob/' . $this->getCommitId() . '/';
        $link .= '{FILE}';
        $link .= '#L{LINE}-L{LINE_END}';

        return $link;
    }

    /**
     * @inheritdoc
     */
    protected function postCloneSetup(Builder $builder, $cloneTo, array $extra = null)
    {
        $success = true;

        try {
            if (Build::SOURCE_WEBHOOK_PULL_REQUEST === $this->getSource()) {
                $pullRequestId = $this->getExtra('pull_request_number');

                $cmd = 'cd "%s" && git checkout -b php-censor/' . $this->getId()
                    . ' %s && git pull -q --no-edit origin pull/%s/head';
                if (!empty($extra['git_ssh_wrapper'])) {
                    $cmd = 'export GIT_SSH="'.$extra['git_ssh_wrapper'].'" && ' . $cmd;
                }
                $success = $builder->executeCommand($cmd, $cloneTo, $this->getBranch(), $pullRequestId);
            }
        } catch (\Exception $ex) {
            $success = false;
        }

        if ($success) {
            $success = parent::postCloneSetup($builder, $cloneTo, $extra);
        }

        return $success;
    }

    /**
     * @inheritdoc
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
        $allowCommentCommit = (boolean)Config::getInstance()->get(
            'php-censor.github.comments.commit',
            false
        );

        $allowCommentPullRequest = (boolean)Config::getInstance()->get(
            'php-censor.github.comments.pull_request',
            false
        );

        if ($allowCommentCommit || $allowCommentPullRequest) {
            $diffLineNumber = $this->getDiffLineNumber($builder, $file, $lineStart);

            if (!is_null($diffLineNumber)) {
                $helper = new Github();

                $repo     = $this->getProject()->getReference();
                $prNumber = $this->getExtra('pull_request_number');
                $commit   = $this->getCommitId();

                if (!empty($prNumber)) {
                    if ($allowCommentPullRequest) {
                        $helper->createPullRequestComment($repo, $prNumber, $commit, $file, $diffLineNumber, $message);
                    }
                } else {
                    if ($allowCommentCommit) {
                        $helper->createCommitComment($repo, $commit, $file, $diffLineNumber, $message);
                    }
                }
            }
        }

        parent::reportError($builder, $plugin, $message, $severity, $file, $lineStart, $lineEnd);
    }

    /**
     * Uses git diff to figure out what the diff line position is, based on the error line number.
     *
     * @param Builder $builder
     * @param string  $file
     * @param integer $line
     *
     * @return integer|null
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
