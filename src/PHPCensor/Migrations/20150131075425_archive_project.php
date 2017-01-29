<?php

use Phinx\Migration\AbstractMigration;

class ArchiveProject extends AbstractMigration
{
    public function up()
    {
        $project = $this->table('project');

        if (!$project->hasColumn('archived')) {
            $project->addColumn('archived', 'boolean', ['default' => false])->save();
        }
    }

    public function down()
    {
        $project = $this->table('project');

        if ($project->hasColumn('archived')) {
            $project->removeColumn('archived')->save();
        }
    }
}
