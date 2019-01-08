<?php

use Phinx\Migration\AbstractMigration;

class UniqueEmailAndNameUserFields extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')
            ->removeIndex(['email'])
            ->save();

        $this
            ->table('user')

            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['name'], ['unique' => true])

            ->save();
    }

    public function down()
    {
        $this
            ->table('user')

            ->removeIndex(['email'])
            ->removeIndex(['name'])

            ->save();

        $this
            ->table('user')
            ->addIndex(['email'])
            ->save();
    }
}
