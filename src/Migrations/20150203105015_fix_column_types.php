<?php

use Phinx\Migration\AbstractMigration;

class FixColumnTypes extends AbstractMigration
{
    public function up()
    {
        $build = $this->table('build');

        $build->changeColumn('log', 'text', ['null'  => true]);

        $buildMeta = $this->table('build_meta');

        $buildMeta->changeColumn('meta_value', 'text', ['null'  => false]);
    }

    public function down()
    {
        $build = $this->table('build');

        $build->changeColumn('log', 'text', ['null' => true]);

        $buildMeta = $this->table('build_meta');

        $buildMeta->changeColumn('meta_value', 'text', ['null' => false]);
    }
}
