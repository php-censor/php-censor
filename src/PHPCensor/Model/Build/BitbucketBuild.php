<?php

namespace PHPCensor\Model\Build;

/**
 * Bitbucket Build Model
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BitbucketBuild extends RemoteGitBuild
{
    /**
    * Get link to commit from another source (i.e. Github)
    */
    public function getCommitLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/commits/' . $this->getCommitId();
    }

    /**
    * Get link to branch from another source (i.e. Github)
    */
    public function getBranchLink()
    {
        return 'https://bitbucket.org/' . $this->getProject()->getReference() . '/src/?at=' . $this->getBranch();
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
}
