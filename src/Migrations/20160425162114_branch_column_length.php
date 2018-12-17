<?php

use Phinx\Migration\AbstractMigration;

class BranchColumnLength extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->changeColumn('branch', 'string', ['limit' => 250, 'default' => 'master'])
            ->save();

        $this
            ->table('project')
            ->changeColumn('branch', 'string', ['limit' => 250, 'default' => 'master'])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->changeColumn('branch', 'string', ['limit' => 50, 'default' => 'master'])
            ->save();

        $this
            ->table('project')
            ->changeColumn('branch', 'string', ['limit' => 50, 'default' => 'master'])
            ->save();
    }
}
