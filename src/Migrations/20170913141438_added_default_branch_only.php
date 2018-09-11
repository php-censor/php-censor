<?php

use Phinx\Migration\AbstractMigration;

class AddedDefaultBranchOnly extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('project');

        if (!$table->hasColumn('default_branch_only')) {
            $table
                ->addColumn('default_branch_only', 'integer', ['default' => 0])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('project');

        if ($table->hasColumn('default_branch_only')) {
            $table
                ->removeColumn('default_branch_only')
                ->save();
        }
    }
}
