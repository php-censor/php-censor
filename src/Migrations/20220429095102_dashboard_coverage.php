<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DashboardCoverage extends AbstractMigration
{
    public function up()
    {
        $builds = $this->table('builds');

        $builds
            ->addColumn('test_coverage', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('test_coverage_previous', 'string', ['limit' => 10, 'null' => true])

            ->save();
    }

    public function down()
    {
        $builds = $this->table('builds');

        $builds
            ->removeColumn('test_coverage')
            ->removeColumn('test_coverage_previous')

            ->save();
    }
}
