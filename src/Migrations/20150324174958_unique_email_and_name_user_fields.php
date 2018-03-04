<?php

use Phinx\Migration\AbstractMigration;

class UniqueEmailAndNameUserFields extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('user');

        if (!$table->hasIndex('email')) {
            $table->addIndex('email', ['unique' => true])->save();
        }

        if (!$table->hasIndex('name')) {
            $table->addIndex('name', ['unique' => true])->save();
        }
    }

    public function down()
    {
        $table = $this->table('user');

        if ($table->hasIndex('email')) {
            $table->removeIndex(['email'])->save();
        }

        if ($table->hasIndex('name')) {
            $table->removeIndex(['name'])->save();
        }
    }
}
