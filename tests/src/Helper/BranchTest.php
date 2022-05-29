<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Helper;

use PHPCensor\Helper\Branch;
use PHPCensor\Model\Base\Project;
use PHPUnit\Framework\TestCase;

class BranchTest extends TestCase
{
    /**
     * @dataProvider providerGetDefaultBranchName
     */
    public function testGetDefaultBranchName(string $projectType, string $expectedBranch): void
    {
        $branch = Branch::getDefaultBranchName($projectType);

        self::assertEquals($expectedBranch, $branch);
    }

    public function providerGetDefaultBranchName(): array
    {
        return [
            'local'            => [Project::TYPE_LOCAL, 'master'],
            'git'              => [Project::TYPE_GIT, 'master'],
            'github'           => [Project::TYPE_GITHUB, 'master'],
            'bitbucket'        => [Project::TYPE_BITBUCKET, 'master'],
            'bitbucket-server' => [Project::TYPE_BITBUCKET_SERVER, 'master'],
            'gitlab'           => [Project::TYPE_GITLAB, 'master'],
            'gogs'             => [Project::TYPE_GOGS, 'master'],
            'svn'              => [Project::TYPE_SVN, 'trunk'],
            'hg'               => [Project::TYPE_HG, 'default'],
            'bitbucket-hg'     => [Project::TYPE_BITBUCKET_HG, 'default']
        ];
    }
}
