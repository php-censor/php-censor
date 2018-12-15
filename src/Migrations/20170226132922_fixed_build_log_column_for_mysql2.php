<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixedBuildLogColumnForMysql2 extends AbstractMigration
{
    public function up()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build')
                ->changeColumn(
                    'log',
                    'text',
                    ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true]
                )
                ->save();
        }
    }

    public function down()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build')
                ->changeColumn('log', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->save();
        }
    }
}
