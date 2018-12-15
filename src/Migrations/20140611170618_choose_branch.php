<?php

use Phinx\Migration\AbstractMigration;

class ChooseBranch extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->addColumn('branch', 'string', ['limit' => 250])
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->removeColumn('branch')
            ->save();
    }
}
