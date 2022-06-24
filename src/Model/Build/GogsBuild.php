<?php

namespace PHPCensor\Model\Build;

/**
 * GogsBuild Build Model
 *
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class GogsBuild extends GitBuild
{
    /**
     * Get a cleaned reference to generate link
     *
     * @return string
     */
    protected function getCleanedReferenceForLink()
    {
        return \preg_replace('/\.git$/i', '', $this->getProject()->getReference());
    }

    /**
     * Get link to commit from Gogs repository
     *
     * @return string
     */
    public function getCommitLink(): string
    {
        return $this->getCleanedReferenceForLink() . '/commit/' . $this->getCommitId();
    }

    /**
     * Get link to branch from Gogs repository
     *
     * @return string
     */
    public function getBranchLink(): string
    {
        return $this->getCleanedReferenceForLink() . '/src/' . $this->getBranch();
    }

    /**
     * Get link to specific file (and line) in a the repo's branch
     *
     * @return string|null
     */
    public function getFileLinkTemplate()
    {
        return \sprintf(
            '%s/src/%s/{FILE}#L{LINE}',
            $this->getCleanedReferenceForLink(),
            $this->getCommitId()
        );
    }
}
