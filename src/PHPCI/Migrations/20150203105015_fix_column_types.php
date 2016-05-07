<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixColumnTypes extends AbstractMigration
{
    public function up()
    {
        $build = $this->table('build');

        $build->changeColumn('log', 'text', ['null'  => true, 'limit' => MysqlAdapter::TEXT_MEDIUM]);

        $buildMeta = $this->table('build_meta');

        $buildMeta->changeColumn('meta_value', 'text', ['null'  => false, 'limit' => MysqlAdapter::TEXT_MEDIUM]);
    }

    public function down()
    {
        $build = $this->table('build');

        $build->changeColumn('log', 'text', ['null' => true]);

        $buildMeta = $this->table('build_meta');

        $buildMeta->changeColumn('meta_value', 'text', ['null' => false]);
    }
}
