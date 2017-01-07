<?php

use Phinx\Migration\AbstractMigration;

class UniqueEmailAndNameUserFields extends AbstractMigration
{
    public function up()
    {
        $user_table = $this->table('user');

        if (!$user_table->hasIndex('email', ['unique' => true])) {
            $user_table->addIndex('email', ['unique' => true])->save();
        }

        if (!$user_table->hasIndex('name', ['unique' => true])) {
            $user_table->addIndex('name', ['unique' => true])->save();
        }
    }

    public function down()
    {
        $user_table = $this->table('user');

        if ($user_table->hasIndex('email', ['unique' => true])) {
            $user_table->removeIndex(['email'], ['unique' => true])->save();
        }

        if ($user_table->hasIndex('name', ['unique' => true])) {
            $user_table->removeIndex(['name'], ['unique' => true])->save();
        }
    }
}
