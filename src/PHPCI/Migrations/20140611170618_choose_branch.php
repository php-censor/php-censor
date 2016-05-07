<?php

use Phinx\Migration\AbstractMigration;

class ChooseBranch extends AbstractMigration
{
    public function up()
    {
        $project = $this->table('project');

        if (!$project->hasColumn('branch')) {
            $project->addColumn('branch', 'string', ['after' => 'reference', 'limit' => 250])->save();
        }
    }

    public function down()
    {
        $project = $this->table('project');

        if ($project->hasColumn('branch')) {
            $project->removeColumn('branch')->save();
        }
    }
}
