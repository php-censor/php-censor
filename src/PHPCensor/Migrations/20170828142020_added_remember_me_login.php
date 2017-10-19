<?php

use Phinx\Migration\AbstractMigration;

class AddedRememberMeLogin extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('user');

        if (!$table->hasColumn('remember_key')) {
            $table
                ->addColumn('remember_key', 'string', ['limit' => 32, 'null' => true])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('user');

        if ($table->hasColumn('remember_key')) {
            $table
                ->removeColumn('remember_key')
                ->save();
        }
    }
}
