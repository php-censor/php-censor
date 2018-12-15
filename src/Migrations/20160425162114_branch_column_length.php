<?php

use Phinx\Migration\AbstractMigration;

class BranchColumnLength extends AbstractMigration
{
    public function up()
    {
        $build = $this->table('build');
        $build->changeColumn('branch', 'string', ['limit' => 250, 'null' => false, 'default' => 'master']);
        $build->save();
        $project = $this->table('project');
        $project->changeColumn('branch', 'string', ['limit' => 250, 'null' => false, 'default' => 'master']);
        $project->save();

     

    }

    public function down()
    {
        $build = $this->table('build');
        $build->changeColumn('branch', 'string', ['limit' => 50, 'null' => false, 'default' => 'master']);
        $build->save();

        $project = $this->table('project');
        $project->changeColumn('branch', 'string', ['limit' => 50, 'null' => false, 'default' => 'master']);
        $project->save();

    }
}
