<?php

use Phinx\Migration\AbstractMigration;

class AddedOverwriteConfigFieldToProject extends AbstractMigration
{
    public function up()
    {
        if ($this->hasTable('project')) {
            $table = $this->table('project');

            if (!$table->hasColumn('overwrite_build_config')) {
                $table
                    ->addColumn('overwrite_build_config', 'integer', ['default' => 1])
                    ->save();
            }
        }
    }

    public function down()
    {
        if ($this->hasTable('project')) {
            $table = $this->table('project');

            if ($table->hasColumn('overwrite_build_config')) {
                $table
                    ->removeColumn('overwrite_build_config')
                    ->save();
            }
        }
    }
}
