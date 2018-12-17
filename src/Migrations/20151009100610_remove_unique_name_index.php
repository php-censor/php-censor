<?php

use Phinx\Migration\AbstractMigration;

class RemoveUniqueNameIndex extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')

            ->removeIndex(['name'])
            ->addIndex(['name'])

            ->save();
    }

    public function down()
    {
        $this
            ->table('user')

            ->removeIndex(['name'])
            ->addIndex(['name'], ['unique' => true])

            ->save();
    }
}
