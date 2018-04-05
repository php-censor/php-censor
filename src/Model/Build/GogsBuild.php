<?php

namespace PHPCensor\Model\Build;

/**
 * GogsBuild Build Model
 */
class GogsBuild extends GitBuild
{
    /**
    * Get link to commit from Gogs repository
    */
    public function getCommitLink()
    {
        return $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
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
