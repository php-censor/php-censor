<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns3 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build_error');

        if ($table->hasColumn('created_date') && !$table->hasColumn('create_date')) {
            $table
                ->renameColumn('created_date', 'create_date')
                ->save();
        }

        $table = $this->table('project');

        if (!$table->hasColumn('create_date')) {
            $table
                ->addColumn('create_date', 'datetime', ['null' => true])
                ->save();
        }

        if (!$table->hasColumn('user_id')) {
            $table
                ->addColumn('user_id', 'integer', ['default' => 0])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('build_error');

        if ($table->hasColumn('create_date') && !$table->hasColumn('created_date')) {
            $table
                ->renameColumn('create_date', 'created_date')
                ->save();
        }

        $table = $this->table('project');

        if ($table->hasColumn('create_date')) {
            $table
                ->removeColumn('create_date')
                ->save();
        }

        if ($table->hasColumn('user_id')) {
            $table
                ->removeColumn('user_id')
                ->save();
        }
    }
}
