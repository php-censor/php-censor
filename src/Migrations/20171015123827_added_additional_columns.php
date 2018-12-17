<?php

use Phinx\Migration\AbstractMigration;

class AddedAdditionalColumns extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')

            ->addColumn('user_id', 'integer', ['default' => 0])

            ->renameColumn('created', 'create_date')
            ->renameColumn('started', 'start_date')
            ->renameColumn('finished', 'finish_date')

            ->save();
    }

    public function down()
    {
        $this
            ->table('build')

            ->removeColumn('user_id')

            ->renameColumn('create_date', 'created')
            ->renameColumn('start_date', 'started')
            ->renameColumn('finish_date', 'finished')

            ->save();
    }
}
