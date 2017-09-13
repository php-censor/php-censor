<?php

use Phinx\Migration\AbstractMigration;

class AddedDefaultBranchOnly extends AbstractMigration
{
    public function up()
    {
        $project = $this->table('project');

        if (!$project->hasColumn('default_branch_only')) {
            $project->addColumn('default_branch_only', 'integer');
        }
    }

    public function down()
    {
        $project = $this->table('project');

        if ($project->hasColumn('default_branch_only')) {
            $project->removeColumn('default_branch_only')->save();
        }
    }
}
