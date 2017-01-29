<?php

use Phinx\Migration\AbstractMigration;

class ErrorsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build_error');

        if (!$this->hasTable('build_error')) {
            $table->create();
        }

        if (!$table->hasColumn('build_id')) {
            $table->addColumn('build_id', 'integer', ['signed' => true])->save();
        }

        if (!$table->hasColumn('plugin')) {
            $table->addColumn('plugin', 'string', ['limit' => 100])->save();
        }

        if (!$table->hasColumn('file')) {
            $table->addColumn('file', 'string', ['limit' => 250, 'null' => true])->save();
        }

        if (!$table->hasColumn('line_start')) {
            $table->addColumn('line_start', 'integer', ['signed' => false, 'null' => true])->save();
        }

        if (!$table->hasColumn('line_end')) {
            $table->addColumn('line_end', 'integer', ['signed' => false, 'null' => true])->save();
        }

        if (!$table->hasColumn('severity')) {
            $table->addColumn('severity', 'integer', ['signed' => false, 'limit' => 255])->save();
        }

        if (!$table->hasColumn('message')) {
            $table->addColumn('message', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('created_date')) {
            $table->addColumn('created_date', 'datetime')->save();
        }

        if (!$table->hasIndex(['build_id', 'created_date'], ['unique' => false])) {
            $table->addIndex(['build_id', 'created_date'], ['unique' => false])->save();
        }

        if (!$table->hasForeignKey('build_id')) {
            $table->addForeignKey('build_id', 'build', 'id', ['delete'=> 'CASCADE', 'update' => 'CASCADE'])->save();
        }
    }

    public function down()
    {
        $table = $this->table('build_error');

        if ($table->hasForeignKey('build_id')) {
            $table->dropForeignKey('build_id')->save();
        }

        if ($this->hasTable('build_error')) {
            $table->drop();
        }
    }
}
