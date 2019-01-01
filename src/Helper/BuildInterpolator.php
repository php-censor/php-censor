<?php

namespace PHPCensor\Helper;

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
     * @param string    $buildPath
     * @param           $url
     *
     * @throws \Exception
     */
    public function setupInterpolationVars(BaseBuild $build, $buildPath, $url)
    {
        $this->interpolationVars = [];

        /** @deprecated This variable deprecated and will be deleted in version 2.0! */
        $this->interpolationVars['%PHPCI%'] = 1;

        $this->interpolationVars['%COMMIT%']         = $build->getCommitId();
        $this->interpolationVars['%SHORT_COMMIT%']   = substr($build->getCommitId(), 0, 7);
        $this->interpolationVars['%COMMIT_EMAIL%']   = $build->getCommitterEmail();
        $this->interpolationVars['%COMMIT_MESSAGE%'] = $build->getCommitMessage();
        $this->interpolationVars['%COMMIT_URI%']     = $build->getCommitLink();
        $this->interpolationVars['%PROJECT%']        = $build->getProjectId();
        $this->interpolationVars['%PROJECT_TITLE%']  = $build->getProjectTitle();
        $this->interpolationVars['%PROJECT_URI%']    = $url . "project/view/" . $build->getProjectId();
        $this->interpolationVars['%BUILD%']          = $build->getId();
        $this->interpolationVars['%BUILD_PATH%']     = $buildPath;
        $this->interpolationVars['%BUILD_URI%']      = $url . "build/view/" . $build->getId();
        $this->interpolationVars['%BRANCH%']         = $build->getBranch();
        $this->interpolationVars['%BRANCH_URI%']     = $build->getBranchLink();
        $this->interpolationVars['%ENVIRONMENT%']    = $build->getEnvironment();

        /** @deprecated This variables deprecated and will be deleted in version 2.0! Use variables '%COMMIT%', '%SHORT_COMMIT%' etc. instead! */
        $this->interpolationVars['%PHPCI_COMMIT%']         = $this->interpolationVars['%COMMIT%'];
        $this->interpolationVars['%PHPCI_SHORT_COMMIT%']   = $this->interpolationVars['%SHORT_COMMIT%'];
        $this->interpolationVars['%PHPCI_COMMIT_EMAIL%']   = $this->interpolationVars['%COMMIT_EMAIL%'];
        $this->interpolationVars['%PHPCI_COMMIT_MESSAGE%'] = $this->interpolationVars['%COMMIT_MESSAGE%'];
        $this->interpolationVars['%PHPCI_COMMIT_URI%']     = $this->interpolationVars['%COMMIT_URI%'];
        $this->interpolationVars['%PHPCI_PROJECT%']        = $this->interpolationVars['%PROJECT%'];
        $this->interpolationVars['%PHPCI_PROJECT_TITLE%']  = $this->interpolationVars['%PROJECT_TITLE%'];
        $this->interpolationVars['%PHPCI_PROJECT_URI%']    = $this->interpolationVars['%PROJECT_URI%'];
        $this->interpolationVars['%PHPCI_BUILD%']          = $this->interpolationVars['%BUILD%'];
        $this->interpolationVars['%PHPCI_BUILD_PATH%']     = $this->interpolationVars['%BUILD_PATH%'];
        $this->interpolationVars['%PHPCI_BUILD_URI%']      = $this->interpolationVars['%BUILD_URI%'];
        $this->interpolationVars['%PHPCI_BRANCH%']         = $this->interpolationVars['%BRANCH%'];
        $this->interpolationVars['%PHPCI_BRANCH_URI%']     = $this->interpolationVars['%BRANCH_URI%'];
        $this->interpolationVars['%PHPCI_ENVIRONMENT%']    = $this->interpolationVars['%ENVIRONMENT%'];

        /** @deprecated This environment variables deprecated and will be deleted in version 2.0! Use variables 'PHP_CENSOR_' instead! */
        putenv('PHPCI=1');
        putenv('PHPCI_COMMIT=' . $this->interpolationVars['%COMMIT%']);
        putenv('PHPCI_SHORT_COMMIT=' . $this->interpolationVars['%SHORT_COMMIT%']);
        putenv('PHPCI_COMMIT_EMAIL=' . $this->interpolationVars['%COMMIT_EMAIL%']);
        putenv('PHPCI_COMMIT_MESSAGE=' . $this->interpolationVars['%COMMIT_MESSAGE%']);
        putenv('PHPCI_COMMIT_URI=' . $this->interpolationVars['%COMMIT_URI%']);
        putenv('PHPCI_PROJECT=' . $this->interpolationVars['%PROJECT%']);
        putenv('PHPCI_PROJECT_TITLE=' . $this->interpolationVars['%PROJECT_TITLE%']);
        putenv('PHPCI_PROJECT_URI=' . $this->interpolationVars['%PROJECT_URI%']);
        putenv('PHPCI_BUILD=' . $this->interpolationVars['%BUILD%']);
        putenv('PHPCI_BUILD_PATH=' . $this->interpolationVars['%BUILD_PATH%']);
        putenv('PHPCI_BUILD_URI=' . $this->interpolationVars['%BUILD_URI%']);
        putenv('PHPCI_BRANCH=' . $this->interpolationVars['%BRANCH%']);
        putenv('PHPCI_BRANCH_URI=' . $this->interpolationVars['%BRANCH_URI%']);
        putenv('PHPCI_ENVIRONMENT=' . $this->interpolationVars['%ENVIRONMENT%']);

        putenv('PHP_CENSOR=1');
        putenv('PHP_CENSOR_COMMIT=' . $this->interpolationVars['%COMMIT%']);
        putenv('PHP_CENSOR_SHORT_COMMIT=' . $this->interpolationVars['%SHORT_COMMIT%']);
        putenv('PHP_CENSOR_COMMIT_EMAIL=' . $this->interpolationVars['%COMMIT_EMAIL%']);
        putenv('PHP_CENSOR_COMMIT_MESSAGE=' . $this->interpolationVars['%COMMIT_MESSAGE%']);
        putenv('PHP_CENSOR_COMMIT_URI=' . $this->interpolationVars['%COMMIT_URI%']);
        putenv('PHP_CENSOR_PROJECT=' . $this->interpolationVars['%PROJECT%']);
        putenv('PHP_CENSOR_PROJECT_TITLE=' . $this->interpolationVars['%PROJECT_TITLE%']);
        putenv('PHP_CENSOR_PROJECT_URI=' . $this->interpolationVars['%PROJECT_URI%']);
        putenv('PHP_CENSOR_BUILD=' . $this->interpolationVars['%BUILD%']);
        putenv('PHP_CENSOR_BUILD_PATH=' . $this->interpolationVars['%BUILD_PATH%']);
        putenv('PHP_CENSOR_BUILD_URI=' . $this->interpolationVars['%BUILD_URI%']);
        putenv('PHP_CENSOR_BRANCH=' . $this->interpolationVars['%BRANCH%']);
        putenv('PHP_CENSOR_BRANCH_URI=' . $this->interpolationVars['%BRANCH_URI%']);
        putenv('PHP_CENSOR_ENVIRONMENT=' . $this->interpolationVars['%ENVIRONMENT%']);
    }

    /**
     * Replace every occurrence of the interpolation vars in the given string
     * Example: "This is build %PHPCI_BUILD%" => "This is build 182"
     *
     * @param string $input
     *
     * @return string
     */
    public function interpolate($input)
    {
        $keys   = array_keys($this->interpolationVars);
        $values = array_values($this->interpolationVars);

        return str_replace($keys, $values, $input);
    }
}
