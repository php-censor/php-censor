<?php

use Phinx\Migration\AbstractMigration;

class ChangeBuildKeysMigration extends AbstractMigration
{
    public function up()
    {
        $project = $this->table('project');

        if (!$project->hasColumn('ssh_private_key') && $project->hasColumn('git_key')) {
            $project->renameColumn('git_key', 'ssh_private_key');
        }

        if (!$project->hasColumn('ssh_public_key') && $project->hasColumn('public_key')) {
            $project->renameColumn('public_key', 'ssh_public_key');
        }
    }

    public function down()
    {
        $project = $this->table('project');

        if (!$project->hasColumn('git_key') && $project->hasColumn('ssh_private_key')) {
            $project->renameColumn('ssh_private_key', 'git_key');
        }

        if (!$project->hasColumn('public_key') && $project->hasColumn('ssh_public_key')) {
            $project->renameColumn('ssh_public_key', 'public_key');
        }
    }
}
