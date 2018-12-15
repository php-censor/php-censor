<?php

use Phinx\Migration\AbstractMigration;

class ChangeBuildKeysMigration extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('project')

            ->renameColumn('git_key', 'ssh_private_key')
            ->renameColumn('public_key', 'ssh_public_key')

            ->save();
    }

    public function down()
    {
        $this
            ->table('project')

            ->renameColumn('ssh_private_key', 'git_key')
            ->renameColumn('ssh_public_key', 'public_key')

            ->save();
    }
}
