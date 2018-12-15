<?php

use Phinx\Migration\AbstractMigration;

class FixDatabaseColumns extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')

            ->changeColumn('log', 'text', ['null' => true])
            ->changeColumn('branch', 'string', ['limit' => 50, 'default' => 'master'])
            ->changeColumn('created', 'datetime', ['null' => true])
            ->changeColumn('started', 'datetime', ['null' => true])
            ->changeColumn('finished', 'datetime', ['null' => true])
            ->changeColumn('committer_email', 'string', ['limit' => 512, 'null' => true])
            ->changeColumn('commit_message', 'text', ['null' => true])
            ->changeColumn('extra', 'text', ['null' => true])

            ->save();

        $this
            ->table('project')

            ->changeColumn('branch', 'string', ['limit' => 50, 'default' => 'master'])
            ->changeColumn('ssh_private_key', 'text', ['null' => true])
            ->changeColumn('ssh_public_key', 'text', ['null' => true])
            ->changeColumn('access_information', 'string', ['limit' => 250, 'null' => true])
            ->changeColumn('last_commit', 'string', ['limit' => 250, 'null' => true])
            ->changeColumn('allow_public_status', 'integer', ['default' => 0])

            ->save();

        $this
            ->table('user')
            ->changeColumn('is_admin', 'integer', ['default' => 0])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')

            ->changeColumn('log', 'text')
            ->changeColumn('branch', 'string', ['limit' => 50])
            ->changeColumn('created', 'datetime')
            ->changeColumn('started', 'datetime')
            ->changeColumn('finished', 'datetime')
            ->changeColumn('committer_email', 'string', ['limit' => 250])
            ->changeColumn('commit_message', 'text')
            ->changeColumn('extra', 'text')

            ->save();
        
        $this
            ->table('project')

            ->changeColumn('branch', 'string', ['limit' => 250])
            ->changeColumn('ssh_private_key', 'text')
            ->changeColumn('ssh_public_key', 'text')
            ->changeColumn('access_information', 'string', ['limit' => 250])
            ->changeColumn('last_commit', 'string', ['limit' => 250])
            ->changeColumn('allow_public_status', 'integer')

            ->save();

        $this
            ->table('user')
            ->changeColumn('is_admin', 'integer')
            ->save();
    }
}
