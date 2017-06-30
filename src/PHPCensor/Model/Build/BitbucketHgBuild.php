<?php

namespace PHPCensor\Model\Build;

/**
 * BitBucket Build Model
 * 
 * @author Artem Bochkov <artem.v.bochkov@gmail.com>
 */
class BitbucketHgBuild extends MercurialBuild
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
     * Get the URL to be used to clone this remote repository.
     */
    protected function getCloneUrl()
    {
        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            return 'ssh://hg@bitbucket.org/' . $this->getProject()->getReference();
        } else {
            return 'https://bitbucket.org/' . $this->getProject()->getReference();
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
            $matches = [];
            preg_match('/[\/:]([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)/', $this->getExtra('remote_url'), $matches);

            $reference = $matches[1];
        }

        $link = 'https://bitbucket.org/' . $reference . '/';
        $link .= 'src/' . $this->getCommitId() . '/';
        $link .= '{FILE}';
        $link .= '#{BASEFILE}-{LINE}';

        return $link;
    }
}
