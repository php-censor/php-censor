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
            $table->addColumn('group_id', 'integer', ['signed'  => true, 'null'    => false, 'default' => 1,])->save();
        }
        
        if (!$table->hasForeignKey('group_id')) {
            $table->addForeignKey('group_id', 'project_group', 'id', ['delete'=> 'RESTRICT', 'update' => 'CASCADE'])->save();
        }
    }

    public function down()
    {
        $table = $this->table('project');

        if ($table->hasForeignKey('group_id')) {
            $table->dropForeignKey('group_id');
        }

        if ($table->hasColumn('group_id')) {
            $table->removeColumn('group_id');
        }

        $table = $this->table('project_group');
        if ($this->hasTable('project_group')) {
            $table->drop();
        }
    }
}
