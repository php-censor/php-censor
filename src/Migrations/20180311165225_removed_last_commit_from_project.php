<?php

use Phinx\Migration\AbstractMigration;

class RemovedLastCommitFromProject extends AbstractMigration
{
    public function up()
    {
        if ($this->hasTable('project')) {
            $table = $this->table('project');

            if ($table->hasColumn('last_commit')) {
                $table
                    ->removeColumn('last_commit')
                    ->save();
            }
        }
    }

    public function down()
    {
        if ($this->hasTable('project')) {
            $table = $this->table('project');

            if (!$table->hasColumn('last_commit')) {
                $table
                    ->addColumn('last_commit', 'string', ['limit' => 250, 'null' => true, 'default' => null])
                    ->save();
            }
        }
    }
}
