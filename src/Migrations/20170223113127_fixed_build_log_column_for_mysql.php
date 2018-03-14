<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixedBuildLogColumnForMysql extends AbstractMigration
{
    public function up()
    {
        $adapter = $this->getAdapter();
        if ($adapter instanceof MysqlAdapter) {
            $this
                ->table('build')
                ->changeColumn(
                    'log',
                    MysqlAdapter::PHINX_TYPE_TEXT,
                    ['limit' => MysqlAdapter::TEXT_LONG]
                )
                ->save();
        }
    }

    public function down()
    {
    }
}
