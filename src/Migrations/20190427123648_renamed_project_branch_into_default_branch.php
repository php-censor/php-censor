<?php

use Phinx\Migration\AbstractMigration;

class RenamedProjectBranchIntoDefaultBranch extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')

            ->renameColumn('branch', 'default_branch')

            ->save();
    }

    public function down()
    {
        $this
            ->table('project')

            ->renameColumn('default_branch', 'branch')

            ->save();
    }
}
