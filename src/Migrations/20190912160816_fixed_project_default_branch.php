<?php

use Phinx\Migration\AbstractMigration;

class FixedProjectDefaultBranch extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')

            ->changeColumn('default_branch', 'string', ['limit' => 250])

            ->save();
    }

    public function down()
    {
        $this
            ->table('project')

            ->changeColumn(
                'default_branch',
                'string',
                ['limit' => 250, 'default' => 'master']
            )

            ->save();
    }
}
