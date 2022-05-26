<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model;
use PHPCensor\Model\Base\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testConstruct(): void
    {
        $environment = new Environment();

        self::assertInstanceOf(Model::class, $environment);
        self::assertInstanceOf(Environment::class, $environment);

        self::assertEquals([
            'id'         => null,
            'project_id' => null,
            'name'       => null,
            'branches'   => [],
        ], $environment->getDataArray());
    }

    public function testId(): void
    {
        $environment = new Environment();

        $result = $environment->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $environment->getId());

        $result = $environment->setId(100);
        self::assertEquals(false, $result);
    }

    public function testProjectId(): void
    {
        $environment = new Environment();

        $result = $environment->setProjectId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $environment->getProjectId());

        $result = $environment->setProjectId(200);
        self::assertEquals(false, $result);
    }

    public function testName(): void
    {
        $environment = new Environment();

        $result = $environment->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $environment->getName());

        $result = $environment->setName('name');
        self::assertEquals(false, $result);
    }

    public function testBranches(): void
    {
        $environment = new Environment();

        $result = $environment->setBranches(['branch-1', 'branch-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['branch-1', 'branch-2'], $environment->getBranches());

        $result = $environment->setBranches(['branch-1', 'branch-2']);
        self::assertEquals(false, $result);


        $environment = new Environment(['branches' => "branch-1\nbranch-2\nbranch-3\n\nbranch-4"]);
        self::assertEquals(['branch-1', 'branch-2', 'branch-3', 'branch-4'], $environment->getBranches());
    }
}
