<?php

use Phinx\Migration\AbstractMigration;

class FixedBuildBranch extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')

            ->changeColumn('branch', 'string', ['limit' => 250])

            ->save();
    }

    public function down()
    {
        $this
            ->table('build')

            ->changeColumn('branch', 'string', ['limit' => 250, 'default' => 'master'])

            ->save();
    }
}
