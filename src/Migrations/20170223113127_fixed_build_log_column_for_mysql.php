<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixedBuildLogColumnForMysql extends AbstractMigration
{
    public function up()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build')
                ->changeColumn('log', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->save();
        }
    }

    public function down()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build')
                ->changeColumn('log', 'text')
                ->save();
        }
    }
}
