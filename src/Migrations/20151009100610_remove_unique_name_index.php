<?php

use Phinx\Migration\AbstractMigration;

class RemoveUniqueNameIndex extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('user');

        if ($user->hasIndex('name')) {
            $user->removeIndex(['name'])->save();
        }

        if (!$user->hasIndex('name')) {
            $user->addIndex(['name'], ['unique' => false])->save();
        }
    }
    public function down()
    {
        $user = $this->table('user');

        if ($user->hasIndex('name')) {
            $user->removeIndex(['name'])->save();
        }

    }
}
