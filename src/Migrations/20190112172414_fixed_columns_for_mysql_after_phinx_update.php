<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class FixedColumnsForMysqlAfterPhinxUpdate extends AbstractMigration
{
    public function up()
    {
        $adapterType = $this->getAdapter()->getAdapterType();

        if ('mysql' === $adapterType) {
            $this
                ->table('build')
                ->changeColumn(
                    'log',
                    'text',
                    ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true]
                )
                ->save();

            $this
                ->table('build_meta')
                ->changeColumn('meta_value', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->save();
        }
    }

    public function down()
    {
        $adapterType = $this->getAdapter()->getAdapterType();

        if ('mysql' === $adapterType) {
            $this
                ->table('build')
                ->changeColumn('log', 'text')
                ->save();

            $this
                ->table('build_meta')
                ->changeColumn('meta_value', 'text')
                ->save();
        }
    }
}
