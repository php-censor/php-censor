<?php

use Phinx\Migration\AbstractMigration;

class AddProjectGroups extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project_group')
            ->addColumn('title', 'string', ['limit' => 100, 'null' => false])
            ->save();

        $this
            ->table('project')
            ->addColumn('group_id', 'integer', ['default' => 1])
            ->addForeignKey(
                'group_id',
                'project_group',
                'id',
                ['delete' => 'RESTRICT', 'update' => 'CASCADE']
            )
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->dropForeignKey('group_id')
            ->save();

        $this
            ->table('project')
            ->removeColumn('group_id')
            ->save();

        $this
            ->table('project_group')
            ->drop()
            ->save();
    }
}
