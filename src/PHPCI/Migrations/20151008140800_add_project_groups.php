<?php

use Phinx\Migration\AbstractMigration;

class AddProjectGroups extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('project_group');
        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', ['limit' => 100, 'null' => false])->save();

            $group = new \PHPCI\Model\ProjectGroup();
            $group->setTitle('Projects');

            \b8\Store\Factory::getStore('ProjectGroup')->save($group);
        }

        $table = $this->table('project');
        if (!$table->hasColumn('group_id')) {
            $table->addColumn('group_id', 'integer', [
                'signed'  => true,
                'null'    => false,
                'default' => 1,
            ]);
            $table->addForeignKey('group_id', 'project_group', 'id', ['delete'=> 'RESTRICT', 'update' => 'CASCADE'])->save();
        }
    }
}
