<?php

use Phinx\Migration\AbstractMigration;

class AddedNewLabelForErrors extends AbstractMigration
{
    public function up()
    {
        if ($this->hasTable('build_error')) {
            $table = $this->table('build_error');

            if (!$table->hasColumn('hash')) {
                $table
                    ->addColumn('hash', 'string', ['limit' => 32, 'default' => ''])
                    ->save();
            }

            if (!$table->hasColumn('is_new')) {
                $table
                    ->addColumn('is_new', 'boolean', ['default' => false])
                    ->save();
            }
        }
    }

    public function down()
    {
        if ($this->hasTable('build_error')) {
            $table = $this->table('build_error');

            if ($table->hasColumn('hash')) {
                $table
                    ->removeColumn('hash')
                    ->save();
            }

            if ($table->hasColumn('is_new')) {
                $table
                    ->removeColumn('is_new')
                    ->save();
            }
        }
    }
}
