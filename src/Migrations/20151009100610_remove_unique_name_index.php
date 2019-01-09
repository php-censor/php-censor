<?php

use Phinx\Migration\AbstractMigration;

class RemoveUniqueNameIndex extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')
            ->removeIndex(['name'])
            ->save();

        $this
            ->table('user')
            ->addIndex(['name'])
            ->save();
    }

    public function down()
    {
        $this
            ->table('user')
            ->removeIndex(['name'])
            ->save();

        $this
            ->table('user')
            ->addIndex(['name'], ['unique' => true])
            ->save();
    }
}
