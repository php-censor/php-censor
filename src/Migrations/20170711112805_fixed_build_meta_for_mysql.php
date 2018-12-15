<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixedBuildMetaForMysql extends AbstractMigration
{
    public function up()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build_meta')
                ->changeColumn('meta_value', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->save();
        }
    }

    public function down()
    {
        if ($this->getAdapter() instanceof MysqlAdapter) {
            $this
                ->table('build_meta')
                ->changeColumn('meta_value', 'text')
                ->save();
        }
    }
}
