<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build');

        if (!$table->hasColumn('user_id')) {
            $table
                ->addColumn('user_id', 'integer', ['default' => 0])
                ->save();
        }

        if ($table->hasColumn('created')) {
            $table
                ->renameColumn('created', 'create_date')
                ->save();
        }

        if ($table->hasColumn('started')) {
            $table
                ->renameColumn('started', 'start_date')
                ->save();
        }

        if ($table->hasColumn('finished')) {
            $table
                ->renameColumn('finished', 'finish_date')
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('build');

        if ($table->hasColumn('user_id')) {
            $table
                ->removeColumn('user_id')
                ->save();
        }

        if ($table->hasColumn('create_date')) {
            $table
                ->renameColumn('create_date', 'created')
                ->save();
        }

        if ($table->hasColumn('start_date')) {
            $table
                ->renameColumn('start_date', 'started')
                ->save();
        }

        if ($table->hasColumn('finish_date')) {
            $table
                ->renameColumn('finish_date', 'finished')
                ->save();
        }
    }
}
