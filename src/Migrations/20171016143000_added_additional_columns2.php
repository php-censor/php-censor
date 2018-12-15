<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns2 extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project_group')

            ->addColumn('create_date', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer', ['default' => 0])

            ->save();
    }

    public function down()
    {
        $this
            ->table('project_group')

            ->removeColumn('create_date')
            ->removeColumn('user_id')

            ->save();
    }
}
