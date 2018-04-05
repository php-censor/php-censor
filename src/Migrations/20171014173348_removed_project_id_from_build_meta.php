<?php

use Phinx\Migration\AbstractMigration;

class RemovedProjectIdFromBuildMeta extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build_meta');

        if ($table->hasForeignKey('project_id')) {
            $table->dropForeignKey('project_id');
        }

        if ($table->hasColumn('project_id')) {
            $table
                ->removeColumn('project_id')
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('build_meta');

        if (!$table->hasColumn('project_id')) {
            $table
                ->addColumn(
                    'project_id',
                    'integer', [
                        'default' => 0,
                        'after' => 'id'
                    ]
                )
                ->addForeignKey('project_id', 'project', 'id', ['delete'=> 'CASCADE', 'update' => 'CASCADE'])
                ->save();
        }
    }
}
