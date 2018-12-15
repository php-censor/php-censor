<?php

use Phinx\Migration\AbstractMigration;

class AddedRememberMeLogin extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')
            ->addColumn('remember_key', 'string', ['limit' => 32, 'null' => true])
            ->save();
    }

    public function down()
    {
        $this
            ->table('user')
            ->removeColumn('remember_key')
            ->save();
    }
}
