<?php

namespace PHPCensor\Model\Build;

/**
 * GogsBuild Build Model
 */
class GogsBuild extends GitBuild
{
    /**
     * Get a cleaned reference to generate link
     */
    protected function getCleanedReferenceForLink()
    {
        return preg_replace('/\.git$/i', '', $this->getProject()->getReference());
    }

    /**
    * Get link to commit from Gogs repository
    */
    public function getCommitLink()
    {
        return $this->getCleanedReferenceForLink() . '/commit/' . $this->getCommitId();
    }

    /**
    * Get link to branch from Gogs repository
    */
    public function getBranchLink()
    {
        return $this->getCleanedReferenceForLink() . '/src/' . $this->getBranch();
    }

    /**
     * Get link to specific file (and line) in a the repo's branch
     */
    public function getFileLinkTemplate()
    {
        return sprintf(
            '%s/src/%s/{FILE}#L{LINE}',
            $this->getCleanedReferenceForLink(),
            $this->getCommitId()
        );
    }
}
