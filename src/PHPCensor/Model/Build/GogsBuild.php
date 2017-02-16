<?php

namespace PHPCensor\Model\Build;

/**
 * GogsBuild Build Model
 */
class GogsBuild extends RemoteGitBuild
{
    /**
    * Get link to commit from Gogs repository
    */
    public function getCommitLink()
    {
        if ($this->getCommitId() !== 'manual'){
            return $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
        }

        return parent::getCommitLink();
    }

    /**
    * Get link to branch from Gogs repository
    */
    public function getBranchLink()
    {
        return $this->getProject()->getReference() . '/src/' . $this->getBranch();
    }
    /**
     * Get link to specific file (and line) in a the repo's branch
     */
    public function getFileLinkTemplate()
    {
        return sprintf(
            '%s/src/%s/{FILE}#L{LINE}',
            $this->getProject()->getReference(),
            $this->getCommitId()
        );
    }
}
