<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Service;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Project;
use PHPCensor\Service\ProjectService;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectServiceTest extends TestCase
{
    private ProjectService $testedService;
    private ProjectStore $projectStore;
    private ConfigurationInterface $configuration;
    private DatabaseManager $databaseManager;
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $this->configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $this->databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $this->projectStore = $this
            ->getMockBuilder('PHPCensor\Store\ProjectStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $this->projectStore
            ->expects($this->any())
            ->method('save')
            ->will(
                $this->returnArgument(0)
            );

        $this->testedService = new ProjectService($this->storeRegistry, $this->projectStore);
    }

    public function testExecuteCreateGithubProject(): void
    {
        $project = $this->testedService->createProject(
            'Test Project',
            'github',
            'php-censor/php-censor1',
            0
        );

        self::assertEquals('Test Project', $project->getTitle());
        self::assertEquals('github', $project->getType());
        self::assertEquals('php-censor/php-censor1', $project->getReference());
        self::assertEquals([], $project->getAccessInformation());

        self::assertEquals(null, $project->getSshPrivateKey());
        self::assertEquals(null, $project->getSshPublicKey());
        self::assertEquals(null, $project->getBuildConfig());
        self::assertEquals(null, $project->getDefaultBranch());
        self::assertEquals(false, $project->getArchived());
        self::assertEquals(false, $project->getDefaultBranchOnly());
        self::assertEquals(true, $project->getOverwriteBuildConfig());
        self::assertEquals(false, $project->getAllowPublicStatus());
        self::assertEquals(1, $project->getGroupId());
        self::assertEquals([], $project->getEnvironmentsNames());
    }

    public function getExecuteCreateGithubProjectAccessInformationData(): array
    {
        return [
            [
                'git@github.com:php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github.com',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'git@github.com:php-censor/php-censor.git',
                ],
            ], [
                'git@sss.github.com:php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'sss.github.com',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'git@sss.github.com:php-censor/php-censor.git',
                ],
            ], [
                'git@172.168.23.4:php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => '172.168.23.4',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'git@172.168.23.4:php-censor/php-censor.git',
                ],
            ], [
                'ssh://git@github.com/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github.com',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'ssh://git@github.com/php-censor/php-censor.git',
                ],
            ], [
                'ssh://git@172.168.23.4/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => '172.168.23.4',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'ssh://git@172.168.23.4/php-censor/php-censor.git',
                ],
            ], [
                'https://github.com/php-censor/php-censor.git', [
                    'domain'    => 'github.com',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'https://github.com/php-censor/php-censor.git',
                ],
            ], [
                'https://172.168.23.4/php-censor/php-censor.git', [
                    'domain'    => '172.168.23.4',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'https://172.168.23.4/php-censor/php-censor.git',
                ],
            ], [
                'http://github.com/php-censor/php-censor.git', [
                    'domain'    => 'github.com',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'http://github.com/php-censor/php-censor.git',
                ],
            ], [
                'git@github.com:443/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github.com',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'git@github.com:443/php-censor/php-censor.git',
                ],
            ], [
                'git@172.168.23.4:443/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => '172.168.23.4',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'git@172.168.23.4:443/php-censor/php-censor.git',
                ],
            ], [
                'ssh://git@github.com:443/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github.com',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'ssh://git@github.com:443/php-censor/php-censor.git',
                ],
            ], [
                'ssh://git@172.168.23.4:443/php-censor/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => '172.168.23.4',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'ssh://git@172.168.23.4:443/php-censor/php-censor.git',
                ],
            ], [
                'https://github.com:443/php-censor/php-censor.git', [
                    'domain'    => 'github.com',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'https://github.com:443/php-censor/php-censor.git',
                ],
            ], [
                'https://172.168.23.4:443/php-censor/php-censor.git', [
                    'domain'    => '172.168.23.4',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'https://172.168.23.4:443/php-censor/php-censor.git',
                ],
            ], [
                'http://github.com:443/php-censor/php-censor.git', [
                    'domain'    => 'github.com',
                    'port'      => '443',
                    'reference' => 'php-censor/php-censor',
                    'origin'    => 'http://github.com:443/php-censor/php-censor.git',
                ],
            ], [
                'git@github:php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github',
                    'reference' => 'php-censor',
                    'origin'    => 'git@github:php-censor.git',
                ],
            ], [
                'ssh://git@github/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github',
                    'reference' => 'php-censor',
                    'origin'    => 'ssh://git@github/php-censor.git',
                ],
            ], [
                'https://github/php-censor.git', [
                    'domain'    => 'github',
                    'reference' => 'php-censor',
                    'origin'    => 'https://github/php-censor.git',
                ],
            ], [
                'http://github/php-censor.git', [
                    'domain'    => 'github',
                    'reference' => 'php-censor',
                    'origin'    => 'http://github/php-censor.git',
                ],
            ], [
                'git@github:443/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github',
                    'port'      => '443',
                    'reference' => 'php-censor',
                    'origin'    => 'git@github:443/php-censor.git',
                ],
            ], [
                'ssh://git@github:443/php-censor.git', [
                    'user'      => 'git',
                    'domain'    => 'github',
                    'port'      => '443',
                    'reference' => 'php-censor',
                    'origin'    => 'ssh://git@github:443/php-censor.git',
                ],
            ], [
                'https://github:443/php-censor.git', [
                    'domain'    => 'github',
                    'port'      => '443',
                    'reference' => 'php-censor',
                    'origin'    => 'https://github:443/php-censor.git',
                ],
            ], [
                'http://github:443/php-censor.git', [
                    'domain'    => 'github',
                    'port'      => '443',
                    'reference' => 'php-censor',
                    'origin'    => 'http://github:443/php-censor.git',
                ],
            ]
        ];
    }

    /**
     * @dataProvider getExecuteCreateGithubProjectAccessInformationData
     */
    public function testExecuteCreateGithubProjectAccessInformation(string $reference, array $accessInformation): void
    {
        $project = $this->testedService->createProject(
            'Test Project',
            'github',
            $reference,
            0
        );

        self::assertEquals($accessInformation, $project->getAccessInformation());
    }

    public function testExecuteCreateProjectWithOptions(): void
    {
        $options = [
            'ssh_private_key'        => 'private',
            'ssh_public_key'         => 'public',
            'allow_public_status'    => true,
            'overwrite_build_config' => false,
            'archived'               => true,
            'default_branch_only'    => true,
            'build_config'           => 'config',
            'default_branch'         => 'testbranch',
            'group'                  => 11,
            'environments'           => 'env1',
        ];

        $returnValue = $this->testedService->createProject(
            'Test Project',
            'github',
            'vendor/project',
            0,
            $options
        );

        self::assertEquals('private', $returnValue->getSshPrivateKey());
        self::assertEquals('public', $returnValue->getSshPublicKey());
        self::assertEquals('config', $returnValue->getBuildConfig());
        self::assertEquals('testbranch', $returnValue->getDefaultBranch());
        self::assertEquals(false, $returnValue->getOverwriteBuildConfig());
        self::assertEquals(true, $returnValue->getArchived());
        self::assertEquals(true, $returnValue->getDefaultBranchOnly());
        self::assertEquals(true, $returnValue->getAllowPublicStatus());
        self::assertEquals(11, $returnValue->getGroupId());
        self::assertEquals([], $returnValue->getEnvironmentsNames());
    }

    public function testExecuteUpdateExistingProject(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setTitle('Before Title');
        $project->setReference('Before Reference');
        $project->setType('github');

        $returnValue = $this->testedService->updateProject($project, 'After Title', 'bitbucket', 'After Reference');

        self::assertEquals('After Title', $returnValue->getTitle());
        self::assertEquals('After Reference', $returnValue->getReference());
        self::assertEquals('bitbucket', $returnValue->getType());
    }

    public function testExecuteEmptyPublicStatus(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setAllowPublicStatus(true);

        $options = [
            'ssh_private_key' => 'private',
            'ssh_public_key'  => 'public',
            'build_config'    => 'config',
        ];

        $returnValue = $this->testedService->updateProject($project, 'Test Project', 'github', 'vendor/project', $options);

        self::assertEquals(false, $returnValue->getAllowPublicStatus());
    }

    public function testExecuteDeleteProject(): void
    {
        $store = $this
            ->getMockBuilder('PHPCensor\Store\ProjectStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $service = new ProjectService($this->storeRegistry, $store);
        $project = new Project($this->storeRegistry);

        self::assertEquals(false, $service->deleteProject($project));

        $project->setId(1);
        self::assertEquals(true, $service->deleteProject($project));
    }
}
