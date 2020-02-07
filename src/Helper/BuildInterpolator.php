<?php

namespace PHPCensor\Helper;

use Exception;
use PHPCensor\Model\Build as BaseBuild;

/**
 * The BuildInterpolator class replaces variables in a string with build-specific information.
 */
class BuildInterpolator
{
    /**
     * An array of key => value pairs that will be used for
     * interpolation and environment variables
     *
     * @var mixed[]
     *
     * @see setupInterpolationVars()
     */
    protected $interpolationVars = [];

    /**
     * Sets the variables that will be used for interpolation.
     *
     * @param BaseBuild $build
     * @param           $url
     *
     * @throws Exception
     */
    public function setupInterpolationVars(BaseBuild $build, $url)
    {
        $this->interpolationVars = [];

        $this->interpolationVars['%COMMIT_ID%']       = $build->getCommitId();
        $this->interpolationVars['%SHORT_COMMIT_ID%'] = substr($build->getCommitId(), 0, 7);
        $this->interpolationVars['%PROJECT_ID%']      = $build->getProjectId();
        $this->interpolationVars['%BUILD_ID%']        = $build->getId();
        $this->interpolationVars['%COMMITTER_EMAIL%'] = $build->getCommitterEmail();
        $this->interpolationVars['%COMMIT_MESSAGE%']  = $build->getCommitMessage();
        $this->interpolationVars['%COMMIT_LINK%']     = $build->getCommitLink();
        $this->interpolationVars['%PROJECT_TITLE%']   = $build->getProjectTitle();
        $this->interpolationVars['%PROJECT_LINK%']    = $url . 'project/view/' . $build->getProjectId();
        $this->interpolationVars['%BUILD_PATH%']      = $build->getBuildPath();
        $this->interpolationVars['%BUILD_LINK%']      = $url . 'build/view/' . $build->getId();
        $this->interpolationVars['%BRANCH%']          = $build->getBranch();
        $this->interpolationVars['%BRANCH_LINK%']     = $build->getBranchLink();
        $this->interpolationVars['%ENVIRONMENT%']     = $build->getEnvironment();

        putenv('PHP_CENSOR=1');
        putenv('PHP_CENSOR_COMMIT_ID=' . $this->interpolationVars['%COMMIT_ID%']);
        putenv('PHP_CENSOR_SHORT_COMMIT_ID=' . $this->interpolationVars['%SHORT_COMMIT_ID%']);
        putenv('PHP_CENSOR_COMMITTER_EMAIL=' . $this->interpolationVars['%COMMITTER_EMAIL%']);
        putenv('PHP_CENSOR_COMMIT_MESSAGE=' . $this->interpolationVars['%COMMIT_MESSAGE%']);
        putenv('PHP_CENSOR_COMMIT_LINK=' . $this->interpolationVars['%COMMIT_LINK%']);
        putenv('PHP_CENSOR_PROJECT_ID=' . $this->interpolationVars['%PROJECT_ID%']);
        putenv('PHP_CENSOR_PROJECT_TITLE=' . $this->interpolationVars['%PROJECT_TITLE%']);
        putenv('PHP_CENSOR_PROJECT_LINK=' . $this->interpolationVars['%PROJECT_LINK%']);
        putenv('PHP_CENSOR_BUILD_ID=' . $this->interpolationVars['%BUILD_ID%']);
        putenv('PHP_CENSOR_BUILD_PATH=' . $this->interpolationVars['%BUILD_PATH%']);
        putenv('PHP_CENSOR_BUILD_LINK=' . $this->interpolationVars['%BUILD_LINK%']);
        putenv('PHP_CENSOR_BRANCH=' . $this->interpolationVars['%BRANCH%']);
        putenv('PHP_CENSOR_BRANCH_LINK=' . $this->interpolationVars['%BRANCH_LINK%']);
        putenv('PHP_CENSOR_ENVIRONMENT=' . $this->interpolationVars['%ENVIRONMENT%']);

        /** @deprecated The variables are deprecated and will be deleted in version 2.0. Use the variables '%COMMIT_ID%', '%SHORT_COMMIT_ID%' etc. instead. */
        $this->interpolationVars['%COMMIT%']       = $this->interpolationVars['%COMMIT_ID%'];
        $this->interpolationVars['%SHORT_COMMIT%'] = $this->interpolationVars['%SHORT_COMMIT_ID%'];
        $this->interpolationVars['%COMMIT_EMAIL%'] = $this->interpolationVars['%COMMITTER_EMAIL%'];
        $this->interpolationVars['%COMMIT_URI%']   = $this->interpolationVars['%COMMIT_LINK%'];
        $this->interpolationVars['%PROJECT%']      = $this->interpolationVars['%PROJECT_ID%'];
        $this->interpolationVars['%PROJECT_URI%']  = $this->interpolationVars['%PROJECT_LINK%'];
        $this->interpolationVars['%BUILD%']        = $this->interpolationVars['%BUILD_ID%'];
        $this->interpolationVars['%BUILD_URI%']    = $this->interpolationVars['%BUILD_LINK%'];
        $this->interpolationVars['%BRANCH_URI%']   = $this->interpolationVars['%BRANCH_LINK%'];

        putenv('PHP_CENSOR_COMMIT=' . $this->interpolationVars['%COMMIT_ID%']);
        putenv('PHP_CENSOR_SHORT_COMMIT=' . $this->interpolationVars['%SHORT_COMMIT_ID%']);
        putenv('PHP_CENSOR_COMMIT_EMAIL=' . $this->interpolationVars['%COMMITTER_EMAIL%']);
        putenv('PHP_CENSOR_COMMIT_URI=' . $this->interpolationVars['%COMMIT_LINK%']);
        putenv('PHP_CENSOR_PROJECT=' . $this->interpolationVars['%PROJECT_ID%']);
        putenv('PHP_CENSOR_PROJECT_URI=' . $this->interpolationVars['%PROJECT_LINK%']);
        putenv('PHP_CENSOR_BUILD=' . $this->interpolationVars['%BUILD_ID%']);
        putenv('PHP_CENSOR_BUILD_URI=' . $this->interpolationVars['%BUILD_LINK%']);
        putenv('PHP_CENSOR_BRANCH_URI=' . $this->interpolationVars['%BRANCH_LINK%']);

        /** @deprecated This environment variables are deprecated and will be deleted in version 2.0. Use the variables 'PHP_CENSOR_' instead. */
        $this->interpolationVars['%PHPCI%']                = 1;
        $this->interpolationVars['%PHPCI_COMMIT%']         = $this->interpolationVars['%COMMIT_ID%'];
        $this->interpolationVars['%PHPCI_SHORT_COMMIT%']   = $this->interpolationVars['%SHORT_COMMIT_ID%'];
        $this->interpolationVars['%PHPCI_COMMIT_EMAIL%']   = $this->interpolationVars['%COMMITTER_EMAIL%'];
        $this->interpolationVars['%PHPCI_COMMIT_MESSAGE%'] = $this->interpolationVars['%COMMIT_MESSAGE%'];
        $this->interpolationVars['%PHPCI_COMMIT_URI%']     = $this->interpolationVars['%COMMIT_LINK%'];
        $this->interpolationVars['%PHPCI_PROJECT%']        = $this->interpolationVars['%PROJECT_ID%'];
        $this->interpolationVars['%PHPCI_PROJECT_TITLE%']  = $this->interpolationVars['%PROJECT_TITLE%'];
        $this->interpolationVars['%PHPCI_PROJECT_URI%']    = $this->interpolationVars['%PROJECT_LINK%'];
        $this->interpolationVars['%PHPCI_BUILD%']          = $this->interpolationVars['%BUILD_ID%'];
        $this->interpolationVars['%PHPCI_BUILD_PATH%']     = $this->interpolationVars['%BUILD_PATH%'];
        $this->interpolationVars['%PHPCI_BUILD_URI%']      = $this->interpolationVars['%BUILD_LINK%'];
        $this->interpolationVars['%PHPCI_BRANCH%']         = $this->interpolationVars['%BRANCH%'];
        $this->interpolationVars['%PHPCI_BRANCH_URI%']     = $this->interpolationVars['%BRANCH_LINK%'];
        $this->interpolationVars['%PHPCI_ENVIRONMENT%']    = $this->interpolationVars['%ENVIRONMENT%'];

        putenv('PHPCI=1');
        putenv('PHPCI_COMMIT=' . $this->interpolationVars['%COMMIT_ID%']);
        putenv('PHPCI_SHORT_COMMIT=' . $this->interpolationVars['%SHORT_COMMIT_ID%']);
        putenv('PHPCI_COMMIT_EMAIL=' . $this->interpolationVars['%COMMITTER_EMAIL%']);
        putenv('PHPCI_COMMIT_MESSAGE=' . $this->interpolationVars['%COMMIT_MESSAGE%']);
        putenv('PHPCI_COMMIT_URI=' . $this->interpolationVars['%COMMIT_LINK%']);
        putenv('PHPCI_PROJECT=' . $this->interpolationVars['%PROJECT_ID%']);
        putenv('PHPCI_PROJECT_TITLE=' . $this->interpolationVars['%PROJECT_TITLE%']);
        putenv('PHPCI_PROJECT_URI=' . $this->interpolationVars['%PROJECT_LINK%']);
        putenv('PHPCI_BUILD=' . $this->interpolationVars['%BUILD_ID%']);
        putenv('PHPCI_BUILD_PATH=' . $this->interpolationVars['%BUILD_PATH%']);
        putenv('PHPCI_BUILD_URI=' . $this->interpolationVars['%BUILD_LINK%']);
        putenv('PHPCI_BRANCH=' . $this->interpolationVars['%BRANCH%']);
        putenv('PHPCI_BRANCH_URI=' . $this->interpolationVars['%BRANCH_LINK%']);
        putenv('PHPCI_ENVIRONMENT=' . $this->interpolationVars['%ENVIRONMENT%']);
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function realtimeInterpolate($input)
    {
        $input = str_replace('%CURRENT_DATE%', \date('Y-m-d'), $input);
        $input = str_replace('%CURRENT_TIME%', \date('H-i-s'), $input);
        $input = str_replace('%CURRENT_DATETIME%', \date('Y-m-d_H-i-s'), $input);

        return $input;
    }

    /**
     * Replace every occurrence of the interpolation vars in the given string
     * Example: "This is build %BUILD_ID%" => "This is build 182"
     *
     * @param string $input
     *
     * @return string
     */
    public function interpolate($input)
    {
        $input = $this->realtimeInterpolate($input);

        $keys   = array_keys($this->interpolationVars);
        $values = array_values($this->interpolationVars);

        return str_replace($keys, $values, $input);
    }
}
