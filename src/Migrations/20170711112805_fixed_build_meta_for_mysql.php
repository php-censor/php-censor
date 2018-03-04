<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixedBuildMetaForMysql extends AbstractMigration
{
    public function up()
    {
        $adapter = $this->getAdapter();
        if ($adapter instanceof MysqlAdapter) {
            $this
                ->table('build_meta')
                ->changeColumn(
                    'meta_value',
                    MysqlAdapter::PHINX_TYPE_TEXT,
                    ['limit' => MysqlAdapter::TEXT_LONG, 'null' => false]
                )
                ->save();
        }
    }

    public function down()
    {
    }
}
