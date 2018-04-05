<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns2 extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('project_group');

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
        $table = $this->table('project_group');

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
