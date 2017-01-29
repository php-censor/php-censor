<?php

use Phinx\Migration\AbstractMigration;

class RemoveUniqueNameIndex extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('user');

        if ($user->hasIndex('name', ['unique' => true])) {
            $user->removeIndex(['name'], ['unique' => true])->save();
        }

        if (!$user->hasIndex('name', ['unique' => true])) {
            $user->addIndex(['name'], ['unique' => false])->save();
        }
    }
}
