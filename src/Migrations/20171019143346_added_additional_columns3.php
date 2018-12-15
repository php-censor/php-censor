<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns3 extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build_error')
            ->renameColumn('created_date', 'create_date')
            ->save();

        $this
            ->table('project')

            ->addColumn('create_date', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer', ['default' => 0])

            ->save();
    }

    public function down()
    {
        $this
            ->table('build_error')
            ->renameColumn('create_date', 'created_date')
            ->save();

        $this
            ->table('project')

            ->removeColumn('create_date')
            ->removeColumn('user_id')

            ->save();
    }
}
