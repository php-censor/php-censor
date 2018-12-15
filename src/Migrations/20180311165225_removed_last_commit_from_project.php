<?php

use Phinx\Migration\AbstractMigration;

class RemovedLastCommitFromProject extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')
            ->removeColumn('last_commit')
            ->save();
    }

    public function down()
    {
        $this
            ->table('project')
            ->addColumn('last_commit', 'string', ['limit' => 250, 'null' => true])
            ->save();
    }
}
