<?php

declare(strict_types=1);

namespace PHPCensor\Model\Build;

use PHPCensor\Helper\Bitbucket;

/**
 * BitbucketHgBuild Build Model
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Artem Bochkov <artem.v.bochkov@gmail.com>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BitbucketHgBuild extends HgBuild
{
    /**
     * Get link to commit from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getCommitLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/commits/' . $this->getCommitId();
    }

    /**
     * Get link to branch from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getBranchLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/src/?at=' . $this->getBranch();
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

        return 'https://bitbucket.org/' . $remoteReference . '/src/?at=' . $remoteBranch;
    }

    /**
     * Get link to tag from another source (i.e. BitBucket)
     *
     * @return string
     */
    public function getTagLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/src/?at=' . $this->getTag();
    }

    /**
     * Get the URL to be used to clone this remote repository.
     *
     * @return string
     */
    protected function getCloneUrl()
    {
        $key = \trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            return 'ssh://hg@bitbucket.org/' . $this->getProject()->getReference();
        } else {
            return 'https://bitbucket.org/' . $this->getProject()->getReference();
        }
    }

    /**
     * Get a template to use for generating links to files.
     *
     * @return string|null
     */
    public function getFileLinkTemplate()
    {
        $bitbucket = new Bitbucket($this->configuration);

        return $bitbucket->getFileLinkTemplate($this);
    }
}
