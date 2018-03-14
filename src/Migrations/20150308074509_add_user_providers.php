<?php

use Phinx\Migration\AbstractMigration;

class AddUserProviders extends AbstractMigration
{
    public function up()
    {
         // Add the provider columns
        $this
            ->table('user')
            // The provider name
            ->addColumn('provider_key', 'string', [
                'default' => 'internal',
                'limit'   => 255
            ])
            // A data used by the provider
            ->addColumn('provider_data', 'string', [
                'null'  => true,
                'limit' => 255
            ])
            ->save();
    }

    public function down()
    {
         // Remove the provider columns
        $this
            ->table('user')
            ->removeColumn('provider_key')
            ->removeColumn('provider_data')
            ->save();
    }
}
