<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class AddUserProviders extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
         // Add the provider columns
        $this
            ->table('user')
            // The provider name
            ->addColumn('provider_key', 'string', array(
                'default' => 'internal',
                'limit' => MysqlAdapter::TEXT_SMALL
            ))
            // A data used by the provider
            ->addColumn('provider_data', 'string', array(
                'null' => true,
                'limit' => MysqlAdapter::TEXT_SMALL
            ))
            ->save();
    }

    /**
     * Migrate Down.
     */
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
