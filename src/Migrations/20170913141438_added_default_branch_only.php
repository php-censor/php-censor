<?php

use Phinx\Migration\AbstractMigration;

class AddedDefaultBranchOnly extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->addColumn('default_branch_only', 'integer', ['default' => 0])
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->removeColumn('default_branch_only')
            ->save();
    }
}
