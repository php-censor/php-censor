<?php

use Phinx\Migration\AbstractMigration;

class RemovedProjectIdFromBuildMeta extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build_meta')

            ->dropForeignKey('project_id')
            ->removeColumn('project_id')

            ->save();
    }

    public function down()
    {
        $this
            ->table('build_meta')

            ->addColumn('project_id', 'integer')
            ->addForeignKey(
                'project_id',
                'project',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )

            ->save();
    }
}
