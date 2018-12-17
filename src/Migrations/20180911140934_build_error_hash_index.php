<?php

use Phinx\Migration\AbstractMigration;

class BuildErrorHashIndex extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build_error')
            ->addIndex(['hash'])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build_error')
            ->removeIndex(['hash'])
            ->save();
    }
}
