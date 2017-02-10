<?php

namespace PHPCensor\Model\Build;

/**
 * GogsBuild Build Model
 */
class GogsBuild extends RemoteGitBuild
{
    /**
    * Get link to commit from Gogs repositorie
    */
    public function getCommitLink()
    {
        if ($this->getCommitId()!="manual"){
        return $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
        }
    }
    /**
    * Get link to branch from Gogs repositorie
    */
    public function getBranchLink()
    {
        return  $this->getProject()->getReference() . '/src/' . $this->getBranch();
    }

}
