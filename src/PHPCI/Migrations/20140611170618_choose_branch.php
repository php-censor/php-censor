<?php

use Phinx\Migration\AbstractMigration;

class ChooseBranch extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $project = $this->table('project');
        if (!$project->hasColumn('branch')) {
            $project->addColumn('branch', 'string', ['after' => 'reference', 'limit' => 250])->save();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $project = $this->table('project');
        if ($project->hasColumn('branch')) {
            $project->removeColumn('branch')->save();
        }
    }
}
