<?php

use Phinx\Migration\AbstractMigration;

class AddedOverwriteConfigFieldToProject extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->addColumn('overwrite_build_config', 'integer', ['default' => 1])
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->removeColumn('overwrite_build_config')
            ->save();
    }
}
