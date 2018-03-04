<?php

use Phinx\Migration\AbstractMigration;

class FixedEnvironments extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build');

        if ($table->hasColumn('environment')) {
            $table
                ->changeColumn('environment', 'string', ['limit' => 250, 'null' => true])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('build');

        if ($table->hasColumn('environment')) {
            $table
                ->changeColumn('environment', 'string', ['limit' => 250, 'null' => false])
                ->save();
        }
    }
}
