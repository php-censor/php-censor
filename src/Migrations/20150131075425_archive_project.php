<?php

use Phinx\Migration\AbstractMigration;

class ArchiveProject extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->addColumn('archived', 'boolean', ['default' => false])
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->removeColumn('archived')
            ->save();
    }
}
