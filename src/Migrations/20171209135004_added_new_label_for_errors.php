<?php

use Phinx\Migration\AbstractMigration;

class AddedNewLabelForErrors extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build_error')

            ->addColumn('hash', 'string', ['limit' => 32, 'default' => ''])
            ->addColumn('is_new', 'boolean', ['default' => false])

            ->save();
    }

    public function down()
    {
        $this
            ->table('build_error')

            ->removeColumn('hash')
            ->removeColumn('is_new')

            ->save();
    }
}
