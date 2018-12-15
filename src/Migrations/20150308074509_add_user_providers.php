<?php

use Phinx\Migration\AbstractMigration;

class AddUserProviders extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')

            ->addColumn('provider_key', 'string', ['default' => 'internal'])
            ->addColumn('provider_data', 'string', ['null'  => true])

            ->save();
    }

    public function down()
    {
        $this
            ->table('user')

            ->removeColumn('provider_key')
            ->removeColumn('provider_data')

            ->save();
    }
}
