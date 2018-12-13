<?php

use Phinx\Migration\AbstractMigration;

class AddProjectGroups extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('project_group');

        if (!$this->hasTable('project_group')) {
            $table->create();
        }

        if (!$table->hasColumn('title')) {
            $table
                ->addColumn('title', 'string', ['limit' => 100, 'null' => false])
                ->save();
        }

        $table = $this->table('project');

        if (!$table->hasColumn('group_id')) {
            $table->addColumn('group_id', 'integer', ['signed' => true, 'null' => false, 'default' => 1])->save();
        }

        if (!$table->hasForeignKey('group_id')) {
            $table->addForeignKey('group_id', 'project_group', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE'])->save();
        }
        $table->save();
    }

    public function down()
    {
        $project = $this->table('project');

        if ($project->hasForeignKey('group_id')) {
            $project->dropForeignKey('group_id');
        }

        if ($project->hasColumn('group_id')) {
            $project->removeColumn('group_id');
        }
        $project->save();


        $project_group = $this->table('project_group');
        if ($this->hasTable('project_group')) {
            $project_group->drop();
        }
        $project_group->save();
        print "--";
    }
}
